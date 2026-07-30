<?php

namespace Database\Seeders\Niches\Roofing;

use App\Enums\EquipmentType;
use App\Enums\LeadStatus;
use App\Enums\MilestoneStatus;
use App\Enums\ProjectStatus;
use App\Models\Lead;
use App\Models\Milestone;
use App\Models\ProgressPhoto;
use App\Models\Project;
use Database\Seeders\Niches\Concerns\SeedsDemoOps;
use Illuminate\Database\Seeder;

class SampleProjectSeeder extends Seeder
{
    use SeedsDemoOps;

    public function run(): void
    {
        $lead = Lead::query()->updateOrCreate(
            ['email' => 'demo.roofing@example.com'],
            [
                'name' => 'Demo Roof Client',
                'phone' => '(210) 555-0177',
                'address' => '318 Argyle Ave, San Antonio, TX 78209',
                'neighborhood' => 'Alamo Heights',
                'estimated_sqft' => 28,
                'service_type' => 'Full Tear-Off & Install',
                'calculated_estimate_low' => 14875.00,
                'calculated_estimate_high' => 19337.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(4)->setTime(9, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'demoroofingalamoheights2026hash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Roof Client',
                'project_title' => 'Alamo Heights Tear-Off & Install',
                'neighborhood' => 'Alamo Heights',
                'contract_value' => 17200.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(3)->toDateString(),
            ],
        );

        $milestones = [
            ['Tear-Off & Deck Inspect', 'Remove shingles and inspect decking.', MilestoneStatus::Completed],
            ['Underlayment & Ice Shield', 'Dry-in with synthetic underlayment.', MilestoneStatus::Completed],
            ['Shingle Install', 'Architectural shingles and ridge vent.', MilestoneStatus::InProgress],
            ['Final Inspection', 'Magnetic sweep, cleanup, and walkthrough.', MilestoneStatus::Pending],
        ];

        foreach ($milestones as [$title, $description, $status]) {
            Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $title],
                ['description' => $description, 'status' => $status],
            );
        }

        ProgressPhoto::query()->where('project_id', $project->id)->delete();

        foreach ([
            ['Tear-Off & Deck Inspect', 'Old layers removed'],
            ['Underlayment & Ice Shield', 'Dry-in complete'],
            ['Shingle Install', 'Field shingles in progress'],
        ] as $i => [$step, $caption]) {
            ProgressPhoto::query()->create([
                'project_id' => $project->id,
                'image_path' => null,
                'caption' => $caption,
                'milestone_step' => $step,
                'created_at' => now()->subDays(3 - $i),
            ]);
        }

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'Summit Install Crew',
            'crew_leader' => 'Diego Ramos',
            'crew_phone' => '(210) 555-0138',
            'crew_color' => 'amber',
            'crew_notes' => 'Tear-off and install crew — dump trailer stays with this team.',
            'proposal_intro' => '<p>This proposal covers a complete tear-off and re-roof at Argyle Ave: removing the existing layers, inspecting and repairing decking, dry-in with synthetic underlayment, and installing architectural shingles with ridge vent.</p><p>Includes full magnetic sweep and haul-off. Workmanship warranty runs 10 years.</p>',
            'proposal_items' => [
                ['Tear-off, haul-off & deck inspection', 0.20],
                ['Synthetic underlayment & ice shield', 0.15],
                ['Architectural shingles & ridge vent', 0.50],
                ['Flashing, cleanup & final inspection', 0.15],
            ],
            'invoice_items' => [
                ['Tear-off & disposal', 0.20],
                ['Underlayment & dry-in', 0.15],
                ['Shingle install (materials + labor)', 0.50],
                ['Flashing, sweep & final inspection', 0.15],
            ],
            'equipment' => [
                ['16ft Dump Trailer', EquipmentType::Vehicle, 'Tear-off haul-off trailer.'],
                ['Coil Nailer Kit', EquipmentType::Tool, 'Three-gun kit with compressor.'],
            ],
            'labor_rate' => 42.00,
            'material_share' => 0.45,
        ]);
    }
}
