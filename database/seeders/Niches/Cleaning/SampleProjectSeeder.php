<?php

namespace Database\Seeders\Niches\Cleaning;

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
            ['email' => 'demo.cleaning@example.com'],
            [
                'name' => 'Demo Clean Client',
                'phone' => '(512) 555-0110',
                'address' => '1204 Avenue F, Austin, TX 78702',
                'neighborhood' => 'Hyde Park',
                'estimated_sqft' => 1800,
                'service_type' => 'Move-In Deep Clean',
                'calculated_estimate_low' => 286.00,
                'calculated_estimate_high' => 343.00,
                'step_reached' => 'booked',
                'status' => LeadStatus::Booked,
                'scheduled_at' => now()->addDays(2)->setTime(10, 0),
                'is_demo' => true,
            ],
        );

        $project = Project::query()->updateOrCreate(
            ['unique_dashboard_hash' => 'democleaninghydepark2026hash01'],
            [
                'lead_id' => $lead->id,
                'client_name' => 'Demo Clean Client',
                'project_title' => 'Hyde Park Move-In Deep Clean',
                'neighborhood' => 'Hyde Park',
                'contract_value' => 320.00,
                'status' => ProjectStatus::Active,
                'started_at' => now()->subDays(1)->toDateString(),
            ],
        );

        $milestones = [
            ['Arrival & Walkthrough', 'Confirm scope and priority rooms with client.', MilestoneStatus::Completed],
            ['Kitchen & Appliances', 'Deep clean kitchen surfaces and appliances.', MilestoneStatus::Completed],
            ['Baths & Floors', 'Sanitize baths and finish all floor surfaces.', MilestoneStatus::InProgress],
            ['Final Detail Pass', 'Windowsills, switches, and client walkthrough.', MilestoneStatus::Pending],
        ];

        foreach ($milestones as [$title, $description, $status]) {
            Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $title],
                ['description' => $description, 'status' => $status],
            );
        }

        ProgressPhoto::query()->where('project_id', $project->id)->delete();

        foreach ([
            ['Arrival & Walkthrough', 'Scope checklist signed'],
            ['Kitchen & Appliances', 'Kitchen after deep clean'],
            ['Baths & Floors', 'Primary bath in progress'],
        ] as $i => [$step, $caption]) {
            ProgressPhoto::query()->create([
                'project_id' => $project->id,
                'image_path' => null,
                'caption' => $caption,
                'milestone_step' => $step,
                'created_at' => now()->subHours(8 - $i),
            ]);
        }

        $this->seedDemoOps($project, $lead, [
            'crew_name' => 'Hyde Park Team',
            'crew_leader' => 'Renata Vargas',
            'crew_phone' => '(512) 555-0164',
            'crew_color' => 'sky',
            'crew_notes' => 'Two-tech deep-clean team — runs Van #2 with the HEPA kit.',
            'proposal_intro' => '<p>Here is the scope for your move-in deep clean at Avenue F. We cover every room top to bottom, with extra time on the kitchen appliances and both baths before the final detail pass.</p><p>Plan on a single visit of roughly four hours with two techs on site.</p>',
            'proposal_items' => [
                ['Kitchen & appliance deep clean', 0.35],
                ['Bathrooms & floor care', 0.35],
                ['Whole-home detail pass', 0.30],
            ],
            'invoice_items' => [
                ['Move-in deep clean — kitchen & appliances', 0.35],
                ['Move-in deep clean — baths & floors', 0.35],
                ['Final detail pass & walkthrough', 0.30],
            ],
            'equipment' => [
                ['Service Van #2', EquipmentType::Vehicle, 'Stocked for move-in/move-out deep cleans.'],
                ['HEPA Backpack Vacuum', EquipmentType::Tool, 'Used on all allergy-sensitive jobs.'],
            ],
            'labor_rate' => 28.00,
            'material_share' => 0.08,
        ]);
    }
}
