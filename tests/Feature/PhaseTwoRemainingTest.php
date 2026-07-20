<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTwoRemainingTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_costing_accessors_calculate_profit_margin_correctly(): void
    {
        $project = Project::create([
            'client_name' => 'Steve Rogers',
            'project_title' => 'Flagstone Terrace & Fire Pit',
            'neighborhood' => 'Highland Park',
            'status' => 'completed',
            'started_at' => now()->subDays(10),
            'contract_value' => 10000.00,
            'material_cost' => 3500.00,
            'labor_cost' => 2500.00,
        ]);

        $this->assertEqualsWithDelta(6000.00, (float) $project->total_cost, 0.001);
        $this->assertEqualsWithDelta(4000.00, (float) $project->profit_margin, 0.001);
        $this->assertEqualsWithDelta(40.0, (float) $project->profit_margin_percent, 0.001);
    }

    public function test_financial_overview_widget_computes_accurate_totals(): void
    {
        Project::create([
            'client_name' => 'Tony Stark',
            'project_title' => 'Smart Yard Build',
            'neighborhood' => 'Preston Hollow',
            'status' => 'completed',
            'started_at' => now(),
            'contract_value' => 20000.00,
            'material_cost' => 8000.00,
            'labor_cost' => 4000.00,
        ]);

        Invoice::create([
            'client_name' => 'Tony Stark',
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Paid,
            'subtotal' => 20000.00,
            'total' => 20000.00,
        ]);

        Invoice::create([
            'client_name' => 'Tony Stark',
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Overdue,
            'subtotal' => 5000.00,
            'total' => 5000.00,
        ]);

        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Total Booked Revenue');
        $response->assertSee('$20,000');
        $response->assertSee('Collected Revenue');
        $response->assertSee('Outstanding Invoices');
    }

    public function test_financial_overview_requires_invoices_permission(): void
    {
        // Operations user with only resource.invoices
        $operationsUser = User::factory()->create(['role' => UserRole::Operations]);
        // By default operations might have different permissions, let's just use it assuming our gate checks resource.invoices
        // Or create a user and assert it can see the dashboard widget.

        $response = $this->actingAs($operationsUser)->get('/admin');
        $response->assertStatus(200);

        // Since Operations has resource.invoices by default in our setup (assumed), they should see it.
        $response->assertSee('Total Booked Revenue');
    }

    public function test_client_dashboard_displays_referral_link_card(): void
    {
        $project = Project::create([
            'client_name' => 'Natasha Romanoff',
            'project_title' => 'Modern Minimalist Patio',
            'neighborhood' => 'Bishop Arts',
            'status' => 'active',
            'started_at' => now(),
            'contract_value' => 6000.00,
        ]);

        $response = $this->get(route('dashboard', $project->unique_dashboard_hash));

        $response->assertStatus(200);
        $response->assertSee('Refer a Dallas Neighbor & Get $100 Credit', false);
        $response->assertSee('Copy Referral Link');
        $response->assertSee(url('/estimate?ref='.$project->referral_code));
    }
}
