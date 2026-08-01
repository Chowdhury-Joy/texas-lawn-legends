<?php

namespace Database\Seeders\Niches\Pest;

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
            ['email' => 'demo.pest@example.com'],
            [
                'name' => 'Demo Pest Client',
                'phone' => '(512) 555-0156',
                'address' => '4500 Teravista Club Dr, Round Rock, TX 78665',
                'neighborhood' => 'Teravista',
                'estimated_sqft' => 2400,
                'service_type' => 'Whole-Home Perimeter Treatment',
                'calculated_estimate_low' => 142.00,
                'calculated_estimate_high' => 168.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(2)->setTime(11, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'demopestteravista2026hash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Pest Client',
                'project_title' => 'Teravista Perimeter Treatment',
                'neighborhood' => 'Teravista',
                'contract_value' => 155.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(1)->toDateString(),
            ],
        );

        $milestones = [
            ['Exterior Inspection', 'Walk perimeter and identify entry points and nests.', MilestoneStatus::Completed],
            ['Perimeter Application', 'Apply barrier treatment around foundation and eaves.', MilestoneStatus::Completed],
            ['Interior Targeted Treat', 'Kitchen, baths, and utility areas as needed.', MilestoneStatus::InProgress],
            ['Service Report', 'Leave treatment notes and next-visit schedule.', MilestoneStatus::Pending],
        ];

        foreach ($milestones as [$title, $description, $status]) {
            Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $title],
                ['description' => $description, 'status' => $status],
            );
        }

        $this->seedDemoProgressPhotos($project, [
            ['Exterior Inspection', 'Perimeter walk complete'],
            ['Perimeter Application', 'Foundation barrier applied'],
            ['Interior Targeted Treat', 'Kitchen treatment in progress'],
        ]);

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'Teravista Route Team',
            'crew_leader' => 'Nina Okafor',
            'crew_phone' => '(512) 555-0187',
            'crew_color' => 'emerald',
            'crew_notes' => 'Round Rock recurring route — licensed applicator on every visit.',
            'proposal_intro' => '<p>Scope for the whole-home perimeter treatment at Teravista Club Dr: exterior inspection for entry points and nests, barrier application around the foundation and eaves, and targeted interior treatment in the kitchen, baths, and utility areas.</p><p>You get a written service report with the next visit date before we leave.</p>',
            'proposal_items' => [
                ['Exterior inspection & perimeter barrier', 0.65],
                ['Interior targeted treatment & report', 0.35],
            ],
            'invoice_items' => [
                ['Perimeter barrier treatment', 0.65],
                ['Interior targeted treatment', 0.35],
            ],
            'equipment' => [
                ['Route Truck 4', EquipmentType::Vehicle, 'Locked chemical storage, Round Rock route.'],
                ['Backpack Power Sprayer', EquipmentType::Tool, 'Used for foundation and eave applications.'],
            ],
            'labor_rate' => 29.00,
            'material_share' => 0.14,
            'funnel_service_type' => 'Whole-Home Perimeter Treatment',
            'funnel_neighborhoods' => ['Teravista', 'Paloma Lake', 'Forest Creek', 'Old Town', 'University Heights', 'Brushy Creek', 'Cat Hollow', 'Round Rock West', 'Lake Creek', 'Walsh Ranch'],
        ]);
    }
}
