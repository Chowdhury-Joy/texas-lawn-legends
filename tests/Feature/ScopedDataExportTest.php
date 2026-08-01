<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lead;
use App\Models\Setting;
use App\Models\User;
use App\Services\DataExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

/**
 * Scoped CSV/ZIP exports from individual resource list pages.
 */
class ScopedDataExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        config(['license.track' => 'a']);
    }

    /**
     * @return array<string, string> archive entry name => contents
     */
    private function zipEntriesOf(string $path): array
    {
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path) === true);

        $entries = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $entries[$name] = (string) $zip->getFromIndex($i);
        }

        $zip->close();

        return $entries;
    }

    public function test_single_table_export_downloads_csv(): void
    {
        Lead::factory()->create(['name' => 'Scoped Lead', 'email' => 'scoped@example.com']);

        $path = app(DataExportService::class)->generateScoped(
            'leads',
            ['leads' => DB::table('leads')->orderBy('id')],
            User::factory()->create(['role' => UserRole::Admin]),
        );

        $this->assertStringEndsWith('.csv', $path);
        $this->assertStringContainsString('leads-export-', basename($path));

        $csv = file_get_contents($path);
        $this->assertStringContainsString('name,email', $csv);
        $this->assertStringContainsString('Scoped Lead', $csv);
        $this->assertStringContainsString('scoped@example.com', $csv);
    }

    public function test_multi_table_export_bundles_related_rows_in_a_zip(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-SCOPED-1',
            'unique_access_token' => 'scoped-token-0000000001',
            'client_name' => 'Scoped Client',
            'issue_date' => today()->toDateString(),
            'status' => InvoiceStatus::Sent,
            'total' => 250,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Mowing',
            'quantity' => 1,
            'unit_price' => 250,
            'total' => 250,
        ]);

        $path = app(DataExportService::class)->generateScoped('invoices', [
            'invoices' => DB::table('invoices')->where('id', $invoice->id),
            'invoice_items' => DB::table('invoice_items')->where('invoice_id', $invoice->id)->orderBy('id'),
        ]);

        $this->assertStringEndsWith('.zip', $path);

        $entries = $this->zipEntriesOf($path);
        $this->assertArrayHasKey('invoices.csv', $entries);
        $this->assertArrayHasKey('invoice_items.csv', $entries);
        $this->assertStringContainsString('Scoped Client', $entries['invoices.csv']);
        $this->assertStringContainsString('Mowing', $entries['invoice_items.csv']);
    }

    public function test_archive_filename_uses_the_business_name(): void
    {
        Setting::set('site_name', 'BrightSide Cleaning', 'string', 'general');

        $path = app(DataExportService::class)->generateScoped(
            'leads',
            ['leads' => DB::table('leads')],
        );

        $this->assertStringContainsString('brightside-cleaning-leads-export-', basename($path));
    }

    public function test_scoped_export_is_logged_in_activity_log(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        app(DataExportService::class)->generateScoped(
            'leads',
            ['leads' => DB::table('leads')],
            $admin,
        );

        $this->assertDatabaseHas('activity_log', [
            'description' => 'Exported leads data',
            'causer_id' => $admin->getKey(),
        ]);
    }

    public function test_track_b_refuses_scoped_export(): void
    {
        config(['license.track' => 'b']);

        $this->expectException(RuntimeException::class);

        app(DataExportService::class)->generateScoped(
            'leads',
            ['leads' => DB::table('leads')],
        );
    }

    public function test_leads_list_shows_export_button_on_track_a(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)
            ->test(ListLeads::class)
            ->assertActionVisible('export');
    }

    public function test_leads_list_hides_export_button_on_track_b(): void
    {
        config(['license.track' => 'b']);

        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)
            ->test(ListLeads::class)
            ->assertActionHidden('export');
    }

    public function test_invoice_export_respects_active_tab(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $today = Invoice::create([
            'invoice_number' => 'INV-TODAY',
            'unique_access_token' => 'today-token-00000000001',
            'client_name' => 'Today Client',
            'issue_date' => today()->toDateString(),
            'status' => InvoiceStatus::Sent,
            'total' => 100,
        ]);

        Invoice::create([
            'invoice_number' => 'INV-OLD',
            'unique_access_token' => 'old-token-000000000001',
            'client_name' => 'Old Client',
            'issue_date' => today()->subMonths(3)->toDateString(),
            'status' => InvoiceStatus::Sent,
            'total' => 200,
        ]);

        InvoiceItem::create([
            'invoice_id' => $today->id,
            'description' => 'Today line',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
        ]);

        $export = app(DataExportService::class);
        $livewire = Livewire::actingAs($admin)
            ->test(ListInvoices::class)
            ->set('activeTab', 'today');

        $invoiceQuery = $livewire->instance()->getTableQueryForExport();

        $queries = [
            'invoices' => $invoiceQuery,
            'invoice_items' => DB::table('invoice_items')
                ->whereIn('invoice_id', $invoiceQuery->clone()->select('invoices.id'))
                ->orderBy('id'),
        ];

        $path = $export->generateScoped('invoices', $queries);
        $entries = $this->zipEntriesOf($path);

        $this->assertStringContainsString('Today Client', $entries['invoices.csv']);
        $this->assertStringNotContainsString('Old Client', $entries['invoices.csv']);
        $this->assertStringContainsString('Today line', $entries['invoice_items.csv']);
    }
}
