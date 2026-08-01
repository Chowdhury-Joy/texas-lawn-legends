<?php

namespace Database\Seeders\Niches\Lawn;

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
            ['email' => 'demo.client@example.com'],
            [
                'name' => 'Demo Client',
                'phone' => '(214) 555-0148',
                'address' => '1420 Kessler Pkwy, Dallas, TX 75208',
                'neighborhood' => 'Kessler Park',
                'estimated_sqft' => 1800,
                'service_type' => 'Full Yard Renovation',
                'calculated_estimate_low' => 9360.00,
                'calculated_estimate_high' => 11700.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(3)->setTime(10, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'demokesslerpark2026renovationhash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Client',
                'project_title' => 'Kessler Park Full Yard Renovation',
                'neighborhood' => 'Kessler Park',
                'contract_value' => 10800.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(6)->toDateString(),
            ],
        );

        $milestones = [
            ['Site Prep & Demolition', 'Clear existing turf, grade the slope, and stage materials.', MilestoneStatus::Completed],
            ['Retaining Wall Build', 'Pour footing and set natural stone retaining wall course by course.', MilestoneStatus::Completed],
            ['Flagstone Patio Install', 'Lay and level premium flagstone patio extension.', MilestoneStatus::InProgress],
            ['Sod & Final Cleanup', 'Roll new climate-resilient sod and complete final walkthrough.', MilestoneStatus::Pending],
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
            ['Site Prep & Demolition', 'Existing turf cleared and hauled off'],
            ['Site Prep & Demolition', 'Slope graded and compacted'],
            ['Retaining Wall Build', 'Concrete footing poured and cured'],
            ['Retaining Wall Build', 'First natural-stone course set and leveled'],
            ['Retaining Wall Build', 'Wall topped out along the back line'],
            ['Flagstone Patio Install', 'Premium flagstone staged on site'],
        ]);

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'North Dallas Crew',
            'crew_leader' => 'Marcus Ellery',
            'crew_phone' => '(214) 555-0192',
            'crew_color' => 'emerald',
            'crew_notes' => 'Hardscape build crew — runs Truck #3 and the stone trailer.',
            'proposal_intro' => '<p>Thanks for having us out to Kessler Park. This proposal covers the full yard renovation: clearing the existing turf, building the natural stone retaining wall, extending the flagstone patio, and finishing with climate-resilient sod.</p><p>Work runs about two weeks from start date, weather permitting.</p>',
            'proposal_items' => [
                ['Site prep, demolition & grading', 0.18],
                ['Natural stone retaining wall', 0.37],
                ['Flagstone patio extension', 0.28],
                ['Sod, cleanup & final walkthrough', 0.17],
            ],
            'invoice_items' => [
                ['Site prep & grading', 0.18],
                ['Retaining wall build (materials + labor)', 0.37],
                ['Flagstone patio extension', 0.28],
                ['Sod install & final cleanup', 0.17],
            ],
            'equipment' => [
                ['F-250 Crew Truck', EquipmentType::Vehicle, 'Tows the stone trailer on hardscape jobs.'],
                ['60" Zero-Turn Mower', EquipmentType::Machinery, 'Primary maintenance-route mower.'],
            ],
            'labor_rate' => 34.00,
            'material_share' => 0.42,
            'funnel_service_type' => 'Full Yard Renovation',
            'funnel_neighborhoods' => ['Kessler Park', 'Oak Cliff', 'Lakewood', 'Bishop Arts', 'East Dallas', 'M Streets', 'Knox-Henderson', 'Design District', 'Lower Greenville', 'Uptown'],
        ]);
    }
}
