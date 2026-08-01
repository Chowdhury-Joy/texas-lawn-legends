<?php

namespace Database\Seeders\Niches\Windows;

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
            ['email' => 'demo.windows@example.com'],
            [
                'name' => 'Demo Window Client',
                'phone' => '(817) 555-0122',
                'address' => '3025 Morton St, Fort Worth, TX 76107',
                'neighborhood' => 'West 7th',
                'estimated_sqft' => 42,
                'service_type' => 'Move-Out Window Detail',
                'calculated_estimate_low' => 108.00,
                'calculated_estimate_high' => 124.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(2)->setTime(10, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'demowindowswest7th2026hash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Window Client',
                'project_title' => 'West 7th Full Window Detail',
                'neighborhood' => 'West 7th',
                'contract_value' => 118.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(1)->toDateString(),
            ],
        );

        $milestones = [
            ['Arrival & Count', 'Confirm pane count and access with client.', MilestoneStatus::Completed],
            ['Exterior Panes', 'Pure-water wash on all reachable exterior glass.', MilestoneStatus::Completed],
            ['Interior & Tracks', 'Interior squeegee and track wipe-down.', MilestoneStatus::InProgress],
            ['Final Inspection', 'Streak check and client walkthrough.', MilestoneStatus::Pending],
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
            ['Arrival & Count', 'Pane count confirmed'],
            ['Exterior Panes', 'Front elevation complete'],
            ['Interior & Tracks', 'Living room in progress'],
        ]);

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'West 7th Glass Team',
            'crew_leader' => 'Priya Nandan',
            'crew_phone' => '(817) 555-0165',
            'crew_color' => 'sky',
            'crew_notes' => 'Two-tech detail team — pure-water pole system for upper floors.',
            'proposal_intro' => '<p>Scope for your move-out window detail on Morton St: pure-water wash on all reachable exterior glass, interior squeegee, and a wipe-down of every track and sill.</p><p>Streak check and walkthrough before we leave.</p>',
            'proposal_items' => [
                ['Exterior pane wash (42 panes)', 0.55],
                ['Interior panes, tracks & sills', 0.45],
            ],
            'invoice_items' => [
                ['Exterior window wash', 0.55],
                ['Interior panes, tracks & sills', 0.45],
            ],
            'equipment' => [
                ['Detail Van', EquipmentType::Vehicle, 'Carries the filtration tank and ladders.'],
                ['Pure-Water Pole System', EquipmentType::Tool, 'Reaches second-storey glass without ladders.'],
            ],
            'labor_rate' => 27.00,
            'material_share' => 0.06,
            'funnel_service_type' => 'Move-Out Window Detail',
            'funnel_neighborhoods' => ['West 7th', 'Near Southside', 'Cultural District', 'Tanglewood', 'Arlington Heights', 'Como', 'Fairmount', 'Ryan Place', 'Berkeley Place', 'Mistletoe Heights'],
        ]);
    }
}
