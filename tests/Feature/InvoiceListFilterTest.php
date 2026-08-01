<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceListFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
    }

    private function makeInvoice(string $issueDate, InvoiceStatus $status = InvoiceStatus::Sent): Invoice
    {
        static $n = 0;
        $n++;

        return Invoice::create([
            'invoice_number' => 'INV-TEST-'.$n,
            'unique_access_token' => 'tok'.str_pad((string) $n, 20, '0'),
            'client_name' => 'Client '.$n,
            'issue_date' => $issueDate,
            'status' => $status,
            'total' => 100,
        ]);
    }

    public function test_list_page_renders_with_tabs_and_renamed_date_column(): void
    {
        $this->makeInvoice(today()->toDateString());

        Livewire::test(ListInvoices::class)
            ->assertSuccessful()
            ->assertSee('Invoice Date')
            ->assertDontSee('Issued')
            ->assertSee('Today')
            ->assertSee('This Week')
            ->assertSee('This Month')
            ->assertSee('Overdue');
    }

    public function test_filter_form_exposes_from_to_and_status(): void
    {
        $this->makeInvoice(today()->toDateString());

        $html = Livewire::test(ListInvoices::class)->html();

        $this->assertStringContainsString('From date', $html);
        $this->assertStringContainsString('To date', $html);
        $this->assertStringContainsString('issued_from', $html);
        $this->assertStringContainsString('issued_until', $html);
    }

    public function test_defaults_to_all_tab_and_shows_older_invoices(): void
    {
        $old = $this->makeInvoice(today()->subMonths(6)->toDateString());

        Livewire::test(ListInvoices::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$old]);
    }

    public function test_today_tab_excludes_other_days(): void
    {
        $todayInvoice = $this->makeInvoice(today()->toDateString());
        $oldInvoice = $this->makeInvoice(today()->subMonths(6)->toDateString());

        Livewire::test(ListInvoices::class)
            ->set('activeTab', 'today')
            ->assertCanSeeTableRecords([$todayInvoice])
            ->assertCanNotSeeTableRecords([$oldInvoice]);
    }

    public function test_all_preset_tabs_render(): void
    {
        $this->makeInvoice(today()->toDateString());

        foreach (['all', 'today', 'this_week', 'this_month', 'overdue'] as $tab) {
            Livewire::test(ListInvoices::class)
                ->set('activeTab', $tab)
                ->assertSuccessful();
        }
    }

    public function test_date_range_filter_bounds_are_inclusive(): void
    {
        $before = $this->makeInvoice('2026-03-01');
        $onFrom = $this->makeInvoice('2026-04-01');
        $inside = $this->makeInvoice('2026-04-15');
        $onTo = $this->makeInvoice('2026-04-30');
        $after = $this->makeInvoice('2026-05-01');

        Livewire::test(ListInvoices::class)
            ->filterTable('issue_date', [
                'issued_from' => '2026-04-01',
                'issued_until' => '2026-04-30',
            ])
            ->assertCanSeeTableRecords([$onFrom, $inside, $onTo])
            ->assertCanNotSeeTableRecords([$before, $after]);
    }

    public function test_status_filter_works_without_any_date(): void
    {
        $paid = $this->makeInvoice('2026-04-15', InvoiceStatus::Paid);
        $sent = $this->makeInvoice('2026-04-16', InvoiceStatus::Sent);

        Livewire::test(ListInvoices::class)
            ->filterTable('status', InvoiceStatus::Paid->value)
            ->assertCanSeeTableRecords([$paid])
            ->assertCanNotSeeTableRecords([$sent]);
    }

    public function test_date_range_and_status_combine(): void
    {
        $match = $this->makeInvoice('2026-04-15', InvoiceStatus::Paid);
        $wrongStatus = $this->makeInvoice('2026-04-16', InvoiceStatus::Sent);
        $wrongDate = $this->makeInvoice('2026-06-01', InvoiceStatus::Paid);

        Livewire::test(ListInvoices::class)
            ->filterTable('issue_date', [
                'issued_from' => '2026-04-01',
                'issued_until' => '2026-04-30',
            ])
            ->filterTable('status', InvoiceStatus::Paid->value)
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$wrongStatus, $wrongDate]);
    }

    public function test_open_ended_from_date_returns_everything_after(): void
    {
        $before = $this->makeInvoice('2026-03-01');
        $after = $this->makeInvoice('2026-05-01');

        Livewire::test(ListInvoices::class)
            ->filterTable('issue_date', ['issued_from' => '2026-04-01'])
            ->assertCanSeeTableRecords([$after])
            ->assertCanNotSeeTableRecords([$before]);
    }

    /**
     * Created in the opposite order to their invoice dates, so a row sorted by
     * created_at would come back reversed. This is what the sort fix guards.
     */
    public function test_default_sort_is_invoice_date_not_created_at(): void
    {
        $newerDate = $this->makeInvoice('2026-04-20');
        $olderDate = $this->makeInvoice('2026-04-01');

        $this->assertTrue($newerDate->created_at <= $olderDate->created_at);

        Livewire::test(ListInvoices::class)
            ->assertCanSeeTableRecords([$newerDate, $olderDate], inOrder: true);
    }
}
