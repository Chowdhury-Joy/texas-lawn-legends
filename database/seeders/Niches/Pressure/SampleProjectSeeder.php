<?php

namespace Database\Seeders\Niches\Pressure;

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

        foreach ($milestones as [$title, $description, $status]) {
            Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $title],
                ['description' => $description, 'status' => $status],
            );
        }

        ProgressPhoto::query()->where('project_id', $project->id)->delete();

        foreach ([
            ['Arrival & Setup', 'Equipment staged at curb'],
            ['Driveway & Walkways', 'Driveway after wash'],
            ['Siding Soft-Wash', 'Siding rinse in progress'],
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
