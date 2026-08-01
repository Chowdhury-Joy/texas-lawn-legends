<?php

namespace Database\Seeders\Niches\Gutters;

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
            ['email' => 'demo.gutters@example.com'],
            [
                'name' => 'Demo Gutter Client',
                'phone' => '(972) 555-0133',
                'address' => '5601 Granite Pkwy, Plano, TX 75024',
                'neighborhood' => 'Legacy West',
                'estimated_sqft' => 185,
                'service_type' => 'Seamless Gutter Replacement',
                'calculated_estimate_low' => 892.00,
                'calculated_estimate_high' => 1070.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(3)->setTime(8, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'demogutterslegacy2026hash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Gutter Client',
                'project_title' => 'Legacy West Gutter Replacement',
                'neighborhood' => 'Legacy West',
                'contract_value' => 985.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(1)->toDateString(),
            ],
        );

        $milestones = [
            ['Tear-Off & Inspect', 'Remove old gutters and check fascia condition.', MilestoneStatus::Completed],
            ['Fabrication & Hang', 'On-site seamless run fabrication and hanger install.', MilestoneStatus::Completed],
            ['Downspouts & Flush', 'Install downspouts and water-test all runs.', MilestoneStatus::InProgress],
            ['Client Walkthrough', 'Final inspection and care instructions.', MilestoneStatus::Pending],
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
            ['Tear-Off & Inspect', 'Old section removed'],
            ['Fabrication & Hang', 'New run hung on rear elevation'],
            ['Downspouts & Flush', 'Downspout install in progress'],
        ]);

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'Plano Seamless Crew',
            'crew_leader' => 'Kyle Duffy',
            'crew_phone' => '(972) 555-0148',
            'crew_color' => 'slate',
            'crew_notes' => 'Runs the on-site roll former — handles all seamless replacements.',
            'proposal_intro' => '<p>This proposal covers replacing 185 linear feet of gutter at Granite Pkwy: tear-off of the old runs, fascia inspection, on-site seamless fabrication, and new hangers, downspouts, and splash blocks.</p><p>Every run gets water-tested before we call it done.</p>',
            'proposal_items' => [
                ['Tear-off & fascia inspection', 0.18],
                ['Seamless fabrication & hanging', 0.52],
                ['Downspouts, elbows & water test', 0.30],
            ],
            'invoice_items' => [
                ['Gutter tear-off & disposal', 0.18],
                ['Seamless gutter fabrication & install', 0.52],
                ['Downspouts & water test', 0.30],
            ],
            'equipment' => [
                ['Gutter Machine Trailer', EquipmentType::Vehicle, 'Houses the roll former and coil stock.'],
                ['Seamless Roll Former', EquipmentType::Machinery, 'Fabricates runs on site to length.'],
            ],
            'labor_rate' => 32.00,
            'material_share' => 0.34,
            'funnel_service_type' => 'Seamless Gutter Replacement',
            'funnel_neighborhoods' => ['Legacy West', 'Willow Bend', 'West Plano', 'Downtown Plano', 'Chase Oaks', 'Deerfield', 'Russell Creek', 'Shoal Creek', 'Haggard Park', 'Parker Road'],
        ]);
    }
}
