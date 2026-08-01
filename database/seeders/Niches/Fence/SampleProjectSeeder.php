<?php

namespace Database\Seeders\Niches\Fence;

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

        $this->seedDemoProgressPhotos($project, [
            ['Layout & Posts', 'Post line set'],
            ['Frame & Pickets', 'Rear elevation framed'],
            ['Gate & Hardware', 'Gate hang in progress'],
        ]);

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'Frisco Build Crew',
            'crew_leader' => 'Andre Salas',
            'crew_phone' => '(469) 555-0159',
            'crew_color' => 'amber',
            'crew_notes' => 'Post-setting and build crew — auger and post truck stay with this team.',
            'proposal_intro' => '<p>This proposal covers 165 linear feet of cedar privacy fence at Legacy Dr: layout and post setting in concrete, board-on-board pickets on a steel-reinforced frame, one gate with heavy-duty hardware, and a stain and seal pass.</p><p>Old fence removal and haul-off is included.</p>',
            'proposal_items' => [
                ['Layout, post holes & concrete', 0.24],
                ['Frame & board-on-board cedar pickets', 0.46],
                ['Gate, hinges & latch hardware', 0.14],
                ['Stain, seal & haul-off', 0.16],
            ],
            'invoice_items' => [
                ['Post setting & concrete', 0.24],
                ['Cedar pickets & framing (materials + labor)', 0.46],
                ['Gate & hardware', 0.14],
                ['Stain, seal & cleanup', 0.16],
            ],
            'equipment' => [
                ['Post Truck & Trailer', EquipmentType::Vehicle, 'Hauls cedar stock and concrete bags.'],
                ['Hydraulic Auger', EquipmentType::Machinery, 'Two-man auger for post holes.'],
            ],
            'labor_rate' => 36.00,
            'material_share' => 0.40,
            'funnel_service_type' => 'Cedar Privacy Fence',
            'funnel_neighborhoods' => ['Starwood', 'Phillips Creek Ranch', 'Newman Village', 'Frisco Lakes', 'Richwoods', 'Plantation Resort', 'Craig Ranch', 'The Trails', 'Panther Creek', 'Hills of Kingswood'],
        ]);
    }
}
