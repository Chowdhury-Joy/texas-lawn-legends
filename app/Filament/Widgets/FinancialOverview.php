<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Filament\Concerns\RestrictedWidget;
use App\Models\Invoice;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverview extends StatsOverviewWidget
{
    use RestrictedWidget;

    /** Above-the-fold headline numbers, and now two queries — render inline. */
    protected static bool $isLazy = false;

    protected static ?int $sort = -2;

    protected static function widgetPermissionKey(): string
    {
        return 'resource.invoices';
    }

    protected function getStats(): array
    {
        // Contract total and average margin in one pass over projects.
        $projectTotals = Project::query()
            ->selectRaw('SUM(contract_value) as booked_revenue')
            ->selectRaw('AVG('.Project::getProfitMarginSql().') as avg_margin')
            ->first();

        $totalBookedRevenue = $projectTotals->booked_revenue ?? 0;
        $avgMargin = (float) ($projectTotals->avg_margin ?? 0);

        // Paid / outstanding / overdue in one pass over invoices.
        $invoiceTotals = Invoice::query()
            ->selectRaw('SUM(CASE WHEN status = ? THEN total ELSE 0 END) as paid_total', [InvoiceStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN total ELSE 0 END) as outstanding_total', [InvoiceStatus::Sent->value, InvoiceStatus::Overdue->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as overdue_count', [InvoiceStatus::Overdue->value])
            ->first();

        $paidInvoicesTotal = $invoiceTotals->paid_total ?? 0;
        $outstandingInvoicesTotal = $invoiceTotals->outstanding_total ?? 0;
        $overdueCount = (int) ($invoiceTotals->overdue_count ?? 0);

        return [
            Stat::make('Total Booked Revenue', '$'.number_format((float) $totalBookedRevenue))
                ->description('All-time project contract total')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Collected Revenue', '$'.number_format((float) $paidInvoicesTotal))
                ->description('Invoices marked as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Outstanding Invoices', '$'.number_format((float) $outstandingInvoicesTotal))
                ->description($overdueCount > 0 ? "{$overdueCount} overdue invoice(s)" : 'Pending client payment')
                ->icon('heroicon-o-clock')
                ->color($overdueCount > 0 ? 'danger' : 'warning'),

            Stat::make('Average Job Margin', number_format((float) $avgMargin, 1).'%')
                ->description('Average profit margin across projects')
                ->icon('heroicon-o-chart-pie')
                ->color($avgMargin >= 40 ? 'success' : 'primary'),
        ];
    }
}
