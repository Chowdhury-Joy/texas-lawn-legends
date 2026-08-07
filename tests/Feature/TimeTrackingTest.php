<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_time_entry_updates_project_labor_cost_if_not_manual()
    {
        $crew = Crew::create(['name' => 'Crew 1']);

        $project = Project::create([
            'project_title' => 'Test Project',
            'client_name' => 'John',
            'neighborhood' => 'Downtown',
            'contract_value' => 1000,
            'use_manual_labor_cost' => false,
            'labor_cost' => 0,
            'started_at' => '2023-01-01',
        ]);

        $entry = TimeEntry::create([
            'crew_id' => $crew->id,
            'project_id' => $project->id,
            'clock_in_at' => '2023-01-01 08:00:00',
            'clock_out_at' => '2023-01-01 10:00:00',
            'hourly_rate' => 50,
        ]);

        $project->refresh();
        $this->assertEquals(100, $entry->calculated_cost);
        $this->assertEquals(100, $project->labor_cost);
    }

    public function test_time_entry_does_not_update_project_labor_cost_if_manual()
    {
        $crew = Crew::create(['name' => 'Crew 1']);

        $project = Project::create([
            'project_title' => 'Test Project',
            'client_name' => 'John',
            'neighborhood' => 'Downtown',
            'contract_value' => 1000,
            'use_manual_labor_cost' => true,
            'labor_cost' => 500,
            'started_at' => '2023-01-01',
        ]);

        TimeEntry::create([
            'crew_id' => $crew->id,
            'project_id' => $project->id,
            'clock_in_at' => '2023-01-01 08:00:00',
            'clock_out_at' => '2023-01-01 10:00:00',
            'hourly_rate' => 50,
        ]);

        $project->refresh();
        $this->assertEquals(500, $project->labor_cost);
    }
}
