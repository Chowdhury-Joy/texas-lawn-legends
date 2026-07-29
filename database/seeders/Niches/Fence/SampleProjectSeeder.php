<?php

namespace Database\Seeders\Niches\Fence;

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
            ['email' => 'demo.fence@example.com'],
            [
                'name' => 'Demo Fence Client',
                'phone' => '(469) 555-0144',
                'address' => '7200 Legacy Dr, Frisco, TX 75034',
                'neighborhood' => 'Starwood',
                'estimated_sqft' => 165,
                'service_type' => 'Cedar Privacy Fence',
                'calculated_estimate_low' => 6850.00,
                'calculated_estimate_high' => 8357.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(4)->setTime(8, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'demofencestarwood2026hash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Fence Client',
                'project_title' => 'Starwood Cedar Privacy Fence',
                'neighborhood' => 'Starwood',
                'contract_value' => 7650.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(2)->toDateString(),
            ],
        );

        $milestones = [
            ['Layout & Posts', 'Stake line, dig posts, and set concrete.', MilestoneStatus::Completed],
            ['Frame & Pickets', 'Install rails and board-on-board pickets.', MilestoneStatus::Completed],
            ['Gate & Hardware', 'Hang gate and install latches and hinges.', MilestoneStatus::InProgress],
            ['Stain & Walkthrough', 'Apply stain/seal and client final inspection.', MilestoneStatus::Pending],
        ];

        foreach ($milestones as [$title, $description, $status]) {
            Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $title],
                ['description' => $description, 'status' => $status],
            );
        }

        ProgressPhoto::query()->where('project_id', $project->id)->delete();

        foreach ([
            ['Layout & Posts', 'Post line set'],
            ['Frame & Pickets', 'Rear elevation framed'],
            ['Gate & Hardware', 'Gate hang in progress'],
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
