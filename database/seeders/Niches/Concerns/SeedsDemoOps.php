<?php

namespace Database\Seeders\Niches\Concerns;

use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Enums\InvoiceStatus;
use App\Enums\LeadStatus;
use App\Enums\ProposalStatus;
use App\Enums\UserRole;
use App\Models\Crew;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\ProgressPhoto;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds the Operations side of a niche model home: one crew, one sent proposal,
 * one sent invoice, two equipment rows, time entries, funnel leads, staff users,
 * and progress photos backed by compressed Unsplash assets.
 *
 * Money is derived from the project's contract value so every niche stays
 * believable without hard-coding amounts in eight places.
 */
trait SeedsDemoOps
{
    /**
     * @param  array{
     *     crew_name: string,
     *     crew_leader: string,
     *     crew_phone: string,
     *     crew_color?: string,
     *     crew_notes?: string,
     *     proposal_intro: string,
     *     proposal_items: array<int, array{0: string, 1: float}>,
     *     invoice_items: array<int, array{0: string, 1: float}>,
     *     invoice_notes?: string,
     *     equipment: array<int, array{0: string, 1: EquipmentType, 2?: string}>,
     *     labor_rate?: float,
     *     material_share?: float,
     *     funnel_service_type?: string,
     *     funnel_neighborhoods?: list<string>,
     *  }  $labels
     */
    protected function seedDemoOps(Project $project, Lead $lead, array $labels): void
    {
        $crew = $this->seedDemoCrew($labels);

        $project->update(['crew_id' => $crew->id]);

        $this->seedDemoProposal($project, $lead, $labels);
        $this->seedDemoInvoice($project, $lead, $labels);
        $this->seedDemoEquipment($crew, $labels);
        $this->seedDemoTimeEntries($project, $crew, $labels);

        // Time entries drive labour cost, so material cost has to be set too or
        // the projects table reports a ~90% profit margin on the bigger jobs.
        $project->update([
            'use_manual_labor_cost' => false,
            'material_cost' => round((float) $project->contract_value * (float) ($labels['material_share'] ?? 0.20), 2),
        ]);

        $this->seedDemoFunnelLeads($lead, $labels);
        $this->seedDemoStaffUsers();
    }

    /**
     * Attach compressed Unsplash demo images from database/seeders/assets/progress-photos.
     *
     * @param  array<int, array{0: string, 1: string}>  $photos  [milestone_step, caption]
     */
    protected function seedDemoProgressPhotos(Project $project, array $photos): void
    {
        ProgressPhoto::query()->where('project_id', $project->id)->delete();

        $assetFiles = $this->ensureDemoProgressPhotoAssets();

        foreach ($photos as $i => [$step, $caption]) {
            $source = $assetFiles[$i % count($assetFiles)];
            $relative = 'progress-photos/demo-seed-'.basename($source);

            if (! Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->put($relative, (string) file_get_contents($source));
            }

            ProgressPhoto::query()->create([
                'project_id' => $project->id,
                'image_path' => $relative,
                'caption' => $caption,
                'milestone_step' => $step,
                'created_at' => now()->subDays(max(0, 6 - $i))->subHours($i),
            ]);
        }
    }

    /**
     * @return list<string> Absolute paths to compressed JPEG assets
     */
    private function ensureDemoProgressPhotoAssets(): array
    {
        $dir = database_path('seeders/assets/progress-photos');
        $files = glob($dir.'/*.jpg') ?: [];
        sort($files);

        if ($files === []) {
            throw new \RuntimeException(
                'Missing demo progress photo assets in database/seeders/assets/progress-photos.',
            );
        }

        return array_values($files);
    }

    /**
     * Stock the lead funnel / weekly chart: 3 leads each in Partial, Qualified,
     * and Contacted, plus one Lost — staggered across the last 7 days.
     *
     * @param  array<string, mixed>  $labels
     */
    private function seedDemoFunnelLeads(Lead $bookedLead, array $labels): void
    {
        $serviceType = (string) ($labels['funnel_service_type'] ?? $bookedLead->service_type ?? 'Estimate Request');
        $neighborhoods = $labels['funnel_neighborhoods'] ?? [
            'Oak Cliff', 'Lakewood', 'East Dallas', 'Bishop Arts', 'Design District',
            'Lower Greenville', 'M Streets', 'Knox-Henderson', 'Deep Ellum', 'Uptown',
        ];

        $people = [
            ['Ava Morales', 'ava.morales.demo@example.com', '(214) 555-0201'],
            ['Ben Carter', 'ben.carter.demo@example.com', '(214) 555-0202'],
            ['Chloe Nguyen', 'chloe.nguyen.demo@example.com', '(214) 555-0203'],
            ['Derek Walsh', 'derek.walsh.demo@example.com', '(214) 555-0204'],
            ['Elena Brooks', 'elena.brooks.demo@example.com', '(214) 555-0205'],
            ['Felix Ortiz', 'felix.ortiz.demo@example.com', '(214) 555-0206'],
            ['Grace Kim', 'grace.kim.demo@example.com', '(214) 555-0207'],
            ['Hugo Patel', 'hugo.patel.demo@example.com', '(214) 555-0208'],
            ['Ivy Sanders', 'ivy.sanders.demo@example.com', '(214) 555-0209'],
            ['Jake Torres', 'jake.torres.demo@example.com', '(214) 555-0210'],
        ];

        // 3 Partial + 3 Qualified + 3 Contacted + 1 Lost
        $stages = [
            LeadStatus::Partial,
            LeadStatus::Partial,
            LeadStatus::Partial,
            LeadStatus::Qualified,
            LeadStatus::Qualified,
            LeadStatus::Qualified,
            LeadStatus::Contacted,
            LeadStatus::Contacted,
            LeadStatus::Contacted,
            LeadStatus::Lost,
        ];

        foreach ($stages as $index => $status) {
            [$name, $email, $phone] = $people[$index];
            $createdAt = now()->subDays($index % 7)->setTime(9 + ($index % 5), 15 + ($index * 3) % 40);

            $payload = [
                'name' => $name,
                'phone' => $phone,
                'address' => null,
                'neighborhood' => $neighborhoods[$index % count($neighborhoods)],
                'estimated_sqft' => match ($status) {
                    LeadStatus::Partial => null,
                    default => 800 + ($index * 120),
                },
                'service_type' => $serviceType,
                'calculated_estimate_low' => match ($status) {
                    LeadStatus::Partial => null,
                    default => 420 + ($index * 85),
                },
                'calculated_estimate_high' => match ($status) {
                    LeadStatus::Partial => null,
                    default => 560 + ($index * 95),
                },
                'step_reached' => match ($status) {
                    LeadStatus::Partial => 'contact',
                    LeadStatus::Qualified => 'estimate',
                    LeadStatus::Contacted => 'estimate',
                    LeadStatus::Lost => 'estimate',
                    default => 'booked',
                },
                'status' => $status,
                'scheduled_at' => null,
                'is_demo' => true,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            Lead::query()->updateOrCreate(['email' => $email], $payload);
        }
    }

    /**
     * Demo staff so Users is not an empty room — same password as the admin seeder.
     */
    private function seedDemoStaffUsers(): void
    {
        $staff = [
            [
                'email' => 'sales@demo.local',
                'name' => 'Maya Chen',
                'role' => UserRole::Sales,
            ],
            [
                'email' => 'ops@demo.local',
                'name' => 'Jordan Blake',
                'role' => UserRole::Operations,
            ],
            [
                'email' => 'books@demo.local',
                'name' => 'Sam Rivera',
                'role' => UserRole::Bookkeeper,
            ],
        ];

        foreach ($staff as $row) {
            User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make('pass'),
                    'role' => $row['role'],
                ],
            );
        }
    }

    private function seedDemoCrew(array $labels): Crew
    {
        return Crew::query()->updateOrCreate(
            ['name' => $labels['crew_name']],
            [
                'leader_name' => $labels['crew_leader'],
                'phone' => $labels['crew_phone'],
                'color' => $labels['crew_color'] ?? 'emerald',
                'notes' => $labels['crew_notes'] ?? null,
            ],
        );
    }

    private function seedDemoProposal(Project $project, Lead $lead, array $labels): void
    {
        $contract = (float) $project->contract_value;

        Proposal::query()->updateOrCreate(
            ['lead_id' => $lead->id, 'project_id' => $project->id],
            [
                'status' => ProposalStatus::Sent,
                'total_amount' => $contract,
                'expires_at' => now()->addDays(14)->toDateString(),
                'accepted_at' => null,
                'content' => [
                    [
                        'type' => 'text_block',
                        'data' => ['content' => $labels['proposal_intro']],
                    ],
                    [
                        'type' => 'pricing_table',
                        'data' => [
                            'title' => 'Itemized Costs',
                            'items' => array_map(
                                fn (array $line): array => [
                                    'type' => 'line_item',
                                    'data' => [
                                        'description' => $line['description'],
                                        'amount' => $line['amount'],
                                    ],
                                ],
                                $this->splitByShare($contract, $labels['proposal_items']),
                            ),
                        ],
                    ],
                ],
            ],
        );
    }

    private function seedDemoInvoice(Project $project, Lead $lead, array $labels): void
    {
        $contract = (float) $project->contract_value;
        $issuedAt = $project->started_at instanceof Carbon
            ? $project->started_at->copy()
            : now();

        $invoice = Invoice::query()->updateOrCreate(
            ['project_id' => $project->id, 'client_email' => $lead->email],
            [
                'lead_id' => $lead->id,
                'client_name' => $lead->name,
                'issue_date' => $issuedAt->toDateString(),
                'due_date' => $issuedAt->copy()->addDays(14)->toDateString(),
                'status' => InvoiceStatus::Sent,
                'notes' => $labels['invoice_notes'] ?? 'Thanks for your business — payment is due within 14 days.',
            ],
        );

        $invoice->items()->delete();

        foreach ($this->splitByShare($contract, $labels['invoice_items']) as $line) {
            $invoice->items()->create([
                'description' => $line['description'],
                'quantity' => 1,
                'unit_price' => $line['amount'],
            ]);
        }

        $invoice->calculateTotals();
    }

    private function seedDemoEquipment(Crew $crew, array $labels): void
    {
        foreach ($labels['equipment'] as $index => $item) {
            [$name, $type] = $item;

            Equipment::query()->updateOrCreate(
                ['name' => $name],
                [
                    'type' => $type,
                    'status' => EquipmentStatus::Active,
                    'crew_id' => $crew->id,
                    'purchase_date' => now()->subMonths(14 + $index)->startOfMonth()->toDateString(),
                    'next_maintenance_at' => now()->addWeeks(3 + $index)->toDateString(),
                    'notes' => $item[2] ?? null,
                ],
            );
        }
    }

    private function seedDemoTimeEntries(Project $project, Crew $crew, array $labels): void
    {
        $rate = (float) ($labels['labor_rate'] ?? 34.00);
        $contract = (float) $project->contract_value;

        // Keep labour under a third of the contract so margins stay plausible,
        // but cap total hours so a big job does not become one 100-hour shift.
        $totalHours = max(1.5, min(($contract * 0.32) / $rate, 24));
        $shiftCount = $totalHours >= 9 ? 3 : 2;
        $shiftHours = max(0.5, round(($totalHours / $shiftCount) * 4) / 4);

        $startedAt = $project->started_at instanceof Carbon
            ? $project->started_at->copy()->startOfDay()
            : now()->startOfDay();

        TimeEntry::query()->where('project_id', $project->id)->delete();

        for ($shift = 0; $shift < $shiftCount; $shift++) {
            $clockIn = $startedAt->copy()->addDays($shift)->setTime(8, 0);

            TimeEntry::query()->create([
                'crew_id' => $crew->id,
                'project_id' => $project->id,
                'clock_in_at' => $clockIn,
                'clock_out_at' => $clockIn->copy()->addMinutes((int) round($shiftHours * 60)),
                'hourly_rate' => $rate,
            ]);
        }
    }

    /**
     * Turn [description, share-of-contract] pairs into rounded amounts, with the
     * final line absorbing rounding drift so the total matches the contract.
     *
     * @param  array<int, array{0: string, 1: float}>  $lines
     * @return array<int, array{description: string, amount: float}>
     */
    private function splitByShare(float $total, array $lines): array
    {
        $result = [];
        $allocated = 0.0;
        $lastIndex = count($lines) - 1;

        foreach ($lines as $index => [$description, $share]) {
            $amount = $index === $lastIndex
                ? round($total - $allocated, 2)
                : round($total * $share, 2);

            $allocated += $amount;
            $result[] = ['description' => $description, 'amount' => $amount];
        }

        return $result;
    }
}
