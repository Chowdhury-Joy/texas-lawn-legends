<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Crew;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrewSchedulingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_crew_and_assign_to_project(): void
    {
        $crew = Crew::create([
            'name' => 'Crew Alpha — Hardscaping',
            'leader_name' => 'Carlos Mendez',
            'phone' => '(214) 555-0192',
            'color' => 'emerald',
        ]);

        $project = Project::create([
            'client_name' => 'John Wick',
            'project_title' => 'Continental Patio Build',
            'neighborhood' => 'Highland Park',
            'status' => 'active',
            'started_at' => now(),
            'contract_value' => 8500.00,
            'crew_id' => $crew->id,
        ]);

        $this->assertEquals($crew->id, $project->fresh()->crew_id);
        $this->assertEquals('Crew Alpha — Hardscaping', $project->crew->name);
        $this->assertCount(1, $crew->projects);
    }

    public function test_crew_deletion_nulls_project_crew_id(): void
    {
        $crew = Crew::create([
            'name' => 'Crew Beta',
            'leader_name' => 'Sam',
            'color' => 'amber',
        ]);

        $project = Project::create([
            'client_name' => 'Arthur Dent',
            'project_title' => 'Garden Wall',
            'neighborhood' => 'Kessler Park',
            'status' => 'active',
            'started_at' => now(),
            'contract_value' => 3200.00,
            'crew_id' => $crew->id,
        ]);

        $crew->forceDelete();

        $this->assertNull($project->fresh()->crew_id);
        $this->assertNotNull($project->fresh());
    }

    public function test_crew_soft_deletion_nulls_project_crew_id(): void
    {
        $crew = Crew::create([
            'name' => 'Crew Gamma',
            'leader_name' => 'John',
            'color' => 'sky',
        ]);

        $project = Project::create([
            'client_name' => 'Ford Prefect',
            'project_title' => 'Towel Rack Install',
            'neighborhood' => 'Deep Ellum',
            'status' => 'active',
            'started_at' => now(),
            'contract_value' => 500.00,
            'crew_id' => $crew->id,
        ]);

        $crew->delete();

        $this->assertNull($project->fresh()->crew_id);
    }

    public function test_admin_can_access_crews_resource_and_schedule_page(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $crewsResponse = $this->actingAs($admin)->get('/admin/crews');
        $scheduleResponse = $this->actingAs($admin)->get('/admin/manage-schedule');

        $crewsResponse->assertStatus(200);
        $scheduleResponse->assertStatus(200);
        $scheduleResponse->assertSee('Schedule Overview');
    }

    public function test_non_admin_cannot_access_schedule_page(): void
    {
        $contentEditor = User::factory()->create(['role' => UserRole::Content]);

        $response = $this->actingAs($contentEditor)->get('/admin/manage-schedule');
        
        $response->assertStatus(403);
    }

    public function test_schedule_page_detects_unassigned_projects(): void
    {
        $unassignedProject = Project::create([
            'client_name' => 'Bruce Wayne',
            'project_title' => 'Manor Sod & Retaining Wall',
            'neighborhood' => 'Preston Hollow',
            'status' => 'scheduled',
            'started_at' => now(),
            'contract_value' => 12000.00,
            'crew_id' => null,
        ]);

        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get('/admin/manage-schedule');

        $response->assertStatus(200);
        $response->assertSee('Unassigned Project(s) Need Crew Allocation');
        $response->assertSee('Manor Sod & Retaining Wall');
    }
}
