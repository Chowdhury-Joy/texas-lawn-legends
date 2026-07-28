<?php

namespace Database\Seeders\Niches\Roofing;

use App\Enums\LeadStatus;
use App\Enums\MilestoneStatus;
use App\Enums\ProjectStatus;
use App\Models\Lead;
use App\Models\Milestone;
use App\Models\ProgressPhoto;
use App\Models\Project;
use Illuminate\Database\Seeder;

class SampleProjectSeeder extends Seeder
{
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
    }
}
