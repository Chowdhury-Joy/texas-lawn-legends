<?php

namespace Database\Seeders\Niches\Pressure;

use App\Enums\EquipmentType;
use App\Enums\LeadStatus;
use App\Enums\MilestoneStatus;
use App\Enums\ProjectStatus;
use App\Models\Lead;
use App\Models\Milestone;
use App\Models\Project;
use Database\Seeders\Niches\Concerns\SeedsDemoOps;
use Illuminate\Database\Seeder;

class SampleProjectSeeder extends Seeder
{
    use SeedsDemoOps;

    public function run(): void
    {
        $lead = Lead::query()->updateOrCreate(
            ['email' => 'demo.pressure@example.com'],
            [
                'name' => 'Demo Wash Client',
                'phone' => '(713) 555-0118',
                'address' => '1842 Yale St, Houston, TX 77008',
                'neighborhood' => 'Heights',
                'estimated_sqft' => 2200,
                'service_type' => 'Driveway & Walkway Wash',
                'calculated_estimate_low' => 185.00,
                'calculated_estimate_high' => 218.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(2)->setTime(9, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'demopressureheights2026hash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Wash Client',
                'project_title' => 'Heights Driveway & Siding Wash',
                'neighborhood' => 'Heights',
                'contract_value' => 205.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(1)->toDateString(),
            ],
        );

        $milestones = [
            ['Arrival & Setup', 'Protect plants and confirm wash zones with client.', MilestoneStatus::Completed],
            ['Driveway & Walkways', 'Surface prep and high-pressure pass on concrete.', MilestoneStatus::Completed],
            ['Siding Soft-Wash', 'Low-pressure detergent application and rinse.', MilestoneStatus::InProgress],
            ['Final Walkthrough', 'Client review and before/after photos.', MilestoneStatus::Pending],
        ];

        foreach ($milestones as $i => [$title, $description, $status]) {
            Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $title],
                [
                    'description' => $description,
                    'status' => $status,
                    // Stagger demo completions off the project start so the
                    // client timeline reads as real dated progress, not a
                    // stack of steps all finished in the same second.
                    'completed_at' => $status === MilestoneStatus::Completed
                        ? $project->started_at?->copy()->addDays($i)->setTime(11, 30)
                        : null,
                ],
            );
        }

        $this->seedDemoProgressPhotos($project, [
            ['Arrival & Setup', 'Equipment staged at curb'],
            ['Driveway & Walkways', 'Driveway after wash'],
            ['Siding Soft-Wash', 'Siding rinse in progress'],
        ]);

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'Heights Wash Crew',
            'crew_leader' => 'Terrance Hobbs',
            'crew_phone' => '(713) 555-0171',
            'crew_color' => 'sky',
            'crew_notes' => 'Runs the hot-water rig — handles concrete and soft-wash jobs.',
            'proposal_intro' => '<p>This covers the exterior wash at Yale St: surface-cleaner pass on the driveway and walkways, then a low-pressure detergent soft-wash and rinse on the siding.</p><p>We tarp and pre-wet landscaping before any detergent goes down.</p>',
            'proposal_items' => [
                ['Driveway & walkway surface clean', 0.55],
                ['Siding soft-wash & rinse', 0.45],
            ],
            'invoice_items' => [
                ['Driveway & walkway pressure wash', 0.55],
                ['Siding soft-wash & rinse', 0.45],
            ],
            'equipment' => [
                ['Wash Rig Trailer', EquipmentType::Vehicle, 'Carries tank, reels, and surface cleaner.'],
                ['4000 PSI Hot-Water Unit', EquipmentType::Machinery, 'Primary pressure unit for concrete work.'],
            ],
            'labor_rate' => 30.00,
            'material_share' => 0.09,
            'funnel_service_type' => 'Driveway & Walkway Wash',
            'funnel_neighborhoods' => ['Heights', 'Montrose', 'Museum District', 'West U', 'Bellaire', 'Rice Village', 'EaDo', 'Midtown', 'Garden Oaks', 'Oak Forest'],
        ]);
    }
}
