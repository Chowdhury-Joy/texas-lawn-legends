<?php

namespace Tests\Feature;

use App\Enums\MilestoneStatus;
use App\Enums\ProjectStatus;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Setting;
use App\Support\Niche\NicheResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDashboardTimelineTest extends TestCase
{
    use RefreshDatabase;

    private function project(): Project
    {
        return Project::create([
            'client_name' => 'Timeline Client',
            'project_title' => 'Timeline Project',
            'neighborhood' => 'Hyde Park',
            'contract_value' => 500,
            'status' => ProjectStatus::Active,
            'started_at' => '2026-07-30',
            'unique_dashboard_hash' => 'timelinetesthash0000000000000001',
        ]);
    }

    /**
     * @param  array<int, array{0: string, 1: MilestoneStatus}>  $steps
     */
    private function seedSteps(Project $project, array $steps): void
    {
        foreach ($steps as [$title, $status]) {
            Milestone::create([
                'project_id' => $project->id,
                'title' => $title,
                'description' => $title.' details.',
                'status' => $status,
            ]);
        }
    }

    private function dashboard(Project $project): string
    {
        return $this->get('/dashboard/'.$project->unique_dashboard_hash)
            ->assertStatus(200)
            ->getContent();
    }

    public function test_dashboard_renders_with_the_new_timeline(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [
            ['Arrival', MilestoneStatus::Completed],
            ['Kitchen', MilestoneStatus::Completed],
            ['Baths', MilestoneStatus::InProgress],
            ['Final Pass', MilestoneStatus::Pending],
        ]);

        $html = $this->dashboard($project);

        $this->assertStringContainsString('Overall Progress', $html);
        $this->assertStringContainsString('Arrival', $html);
        $this->assertStringContainsString('Final Pass', $html);
    }

    /**
     * The whole point of the redesign: the in-progress step is called out as
     * the current one, with the following step named as what comes next.
     */
    public function test_current_and_next_step_are_called_out(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [
            ['Arrival', MilestoneStatus::Completed],
            ['Baths', MilestoneStatus::InProgress],
            ['Final Pass', MilestoneStatus::Pending],
        ]);

        $html = $this->dashboard($project);

        $this->assertStringContainsString('Happening now', $html);
        $this->assertStringContainsString('Then', $html);
    }

    public function test_falls_back_to_first_pending_step_when_nothing_is_in_progress(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [
            ['Arrival', MilestoneStatus::Completed],
            ['Baths', MilestoneStatus::Pending],
        ]);

        $html = $this->dashboard($project);

        $this->assertStringContainsString('Up next', $html);
        $this->assertStringNotContainsString('Happening now', $html);
    }

    public function test_completed_steps_show_a_date(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [['Arrival', MilestoneStatus::Completed]]);

        $milestone = $project->milestones()->first();
        $milestone->update(['completed_at' => '2026-07-31 11:30:00']);

        $this->assertStringContainsString('Jul 31, 2026', $this->dashboard($project));
    }

    /**
     * A project where every step is done must not crash on the "current step"
     * callout — there is no current step to show.
     */
    public function test_fully_completed_project_renders(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [
            ['Arrival', MilestoneStatus::Completed],
            ['Baths', MilestoneStatus::Completed],
        ]);

        $html = $this->dashboard($project);

        $this->assertStringNotContainsString('Happening now', $html);
        $this->assertStringNotContainsString('Up next', $html);
    }

    public function test_project_with_no_milestones_renders(): void
    {
        $this->dashboard($this->project());
    }

    /**
     * The bug this replaced: "Build Timeline" was hardcoded, so a Move-In Deep
     * Clean and a pest treatment both told the client about a "build".
     */
    public function test_timeline_heading_follows_the_active_niche(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [['Arrival', MilestoneStatus::Completed]]);

        NicheResolver::flush();
        $this->assertStringContainsString('Build Timeline', $this->dashboard($project));

        Setting::set('active_niche', 'cleaning', 'string', 'product');
        NicheResolver::flush();

        $html = $this->dashboard($project);
        $this->assertStringContainsString('Service Timeline', $html);
        $this->assertStringNotContainsString('Build Timeline', $html);

        Setting::set('active_niche', 'pest', 'string', 'product');
        NicheResolver::flush();

        $this->assertStringContainsString('Treatment Timeline', $this->dashboard($project));
    }

    public function test_completing_a_step_stamps_the_date_automatically(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [['Arrival', MilestoneStatus::Pending]]);

        $milestone = $project->milestones()->first();
        $this->assertNull($milestone->completed_at);

        $milestone->update(['status' => MilestoneStatus::Completed]);
        $this->assertNotNull($milestone->fresh()->completed_at);
    }

    public function test_reopening_a_step_clears_a_stale_completion_date(): void
    {
        $project = $this->project();
        $this->seedSteps($project, [['Arrival', MilestoneStatus::Completed]]);

        $milestone = $project->milestones()->first();
        $this->assertNotNull($milestone->completed_at);

        $milestone->update(['status' => MilestoneStatus::InProgress]);
        $this->assertNull($milestone->fresh()->completed_at);
    }

    public function test_an_explicit_completion_date_is_not_overwritten(): void
    {
        $project = $this->project();

        $milestone = Milestone::create([
            'project_id' => $project->id,
            'title' => 'Backdated',
            'status' => MilestoneStatus::Completed,
            'completed_at' => '2026-07-31 11:30:00',
        ]);

        $this->assertSame('2026-07-31', $milestone->fresh()->completed_at->toDateString());
    }

    public function test_every_pack_ships_a_timeline_heading(): void
    {
        foreach (array_keys(config('niche.packs')) as $id) {
            Setting::set('active_niche', $id, 'string', 'product');
            NicheResolver::flush();

            $this->assertNotSame(
                'timeline_heading',
                niche_label('timeline_heading'),
                "Industry pack [{$id}] is missing a timeline_heading label.",
            );
        }
    }
}
