<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicingSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_auto_generates_unique_number(): void
    {
        $invoice1 = Invoice::create([
            'client_name' => 'John Doe',
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Draft,
        ]);

        $invoice2 = Invoice::create([
            'client_name' => 'Jane Smith',
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Draft,
        ]);

        $this->assertNotEmpty($invoice1->invoice_number);
        $this->assertNotEmpty($invoice2->invoice_number);
        $this->assertNotEquals($invoice1->invoice_number, $invoice2->invoice_number);
        $this->assertStringStartsWith('INV-', $invoice1->invoice_number);
    }

    public function test_invoice_item_calculates_amount_and_totals(): void
    {
        $invoice = Invoice::create([
            'client_name' => 'Michael Scott',
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Draft,
        ]);

        $item1 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Patio Build',
            'quantity' => 2,
            'unit_price' => 1500.00,
        ]);

        $item2 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Flagstone Sealing',
            'quantity' => 1,
            'unit_price' => 500.00,
        ]);

        $this->assertEqualsWithDelta(3000.00, (float) $item1->amount, 0.001);
        $this->assertEqualsWithDelta(500.00, (float) $item2->amount, 0.001);

        $invoice->calculateTotals();

        $fresh = $invoice->fresh();
        $this->assertEqualsWithDelta(3500.00, (float) $fresh->subtotal, 0.001);
        $this->assertEqualsWithDelta(3500.00, (float) $fresh->total, 0.001);
    }

    public function test_admin_can_access_invoices_resource(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get('/admin/invoices');

        $response->assertStatus(200);
    }

    public function test_printable_invoice_route_renders_successfully(): void
    {
        $invoice = Invoice::create([
            'client_name' => 'Dwight Schrute',
            'client_email' => 'dwight@schrute-farms.com',
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Sent,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Irrigation System Install',
            'quantity' => 1,
            'unit_price' => 2400.00,
        ]);

        $invoice->calculateTotals();

        $response = $this->get(route('invoices.show', $invoice->unique_access_token));

        $response->assertStatus(200);
        $response->assertSee('INVOICE');
        $response->assertSee('#'.$invoice->invoice_number);
        $response->assertSee('Dwight Schrute');
        $response->assertSee('Irrigation System Install');
        $response->assertSee('$2,400.00');
    }

    public function test_invalid_invoice_access_token_returns_404(): void
    {
        $response = $this->get(route('invoices.show', 'invalid-token-123'));
        $response->assertStatus(404);
    }

    public function test_client_portal_displays_project_invoices(): void
    {
        $project = Project::create([
            'client_name' => 'Pam Beesly',
            'project_title' => 'Art Studio Landscaping',
            'neighborhood' => 'Scranton',
            'status' => 'active',
            'started_at' => now(),
            'contract_value' => 4500.00,
        ]);

        $invoice = Invoice::create([
            'project_id' => $project->id,
            'client_name' => $project->client_name,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Sent,
            'subtotal' => 4500.00,
            'total' => 4500.00,
        ]);

        $response = $this->get(route('dashboard', $project->unique_dashboard_hash));

        $response->assertStatus(200);
        $response->assertSee('Invoices & Billing', false);
        $response->assertSee('#'.$invoice->invoice_number);
        $response->assertSee('$4,500.00');
        $response->assertSee(route('invoices.show', $invoice->unique_access_token));
    }

    public function test_invoice_number_overflows_gracefully(): void
    {
        $year = date('Y');

        Invoice::create([
            'client_name' => 'Overflow Test',
            'issue_date' => now(),
            'invoice_number' => "INV-{$year}-9999",
        ]);

        $nextInvoice = Invoice::create([
            'client_name' => 'Overflow Test 2',
            'issue_date' => now(),
        ]);

        $this->assertEquals("INV-{$year}-10000", $nextInvoice->invoice_number);
    }
}
