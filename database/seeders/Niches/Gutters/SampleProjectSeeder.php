<?php

namespace Database\Seeders\Niches\Gutters;

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

        foreach ($milestones as [$title, $description, $status]) {
            Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $title],
                ['description' => $description, 'status' => $status],
            );
        }

        ProgressPhoto::query()->where('project_id', $project->id)->delete();

        foreach ([
            ['Tear-Off & Inspect', 'Old section removed'],
            ['Fabrication & Hang', 'New run hung on rear elevation'],
            ['Downspouts & Flush', 'Downspout install in progress'],
        ] as $i => [$step, $caption]) {
            ProgressPhoto::query()->create([
                'project_id' => $project->id,
                'image_path' => null,
                'caption' => $caption,
                'milestone_step' => $step,
                'created_at' => now()->subHours(8 - $i),
            ]);
        }
    }
}
