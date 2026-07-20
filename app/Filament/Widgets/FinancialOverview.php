<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Filament\Concerns\RestrictedWidget;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverview extends StatsOverviewWidget
{
    use RestrictedWidget;

    protected static bool $isLazy = false;

    protected static ?int $sort = -2;

    protected static function widgetPermissionKey(): string
    {
        return 'resource.invoices';
    }

    protected function getStats(): array
    {
        $totalBookedRevenue = Project::query()->sum('contract_value');

        $paidInvoicesTotal = Invoice::query()
            ->where('status', InvoiceStatus::Paid)
            ->sum('total');

        $outstandingInvoicesTotal = Invoice::query()
            ->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::Overdue])
            ->sum('total');

        $overdueCount = Invoice::query()
            ->where('status', InvoiceStatus::Overdue)
            ->count();

        $avgMargin = (float) Project::query()->avg(
            DB::raw("((contract_value - material_cost - labor_cost) / NULLIF(contract_value, 0)) * 100")
        );

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
