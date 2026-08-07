<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Chart';

    /**
     * SQL producing a `YYYY-MM` bucket for the given column on the active driver.
     */
    private function monthExpression(string $column): string
    {
        return match (Project::query()->getConnection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    protected function getData(): array
    {
        $months = collect(range(11, 0))
            ->map(fn (int $ago) => Carbon::now()->startOfMonth()->subMonths($ago));

        $windowStart = $months->first();
        $labels = $months->map(fn (Carbon $month) => $month->format('M'))->all();

        // Two grouped range queries replace 24 per-month aggregates.
        $bookedByMonth = Project::query()
            ->where('created_at', '>=', $windowStart)
            ->selectRaw($this->monthExpression('created_at').' as period, SUM(contract_value) as aggregate')
            ->groupBy('period')
            ->pluck('aggregate', 'period');

        $collectedByMonth = Invoice::query()
            ->where('status', InvoiceStatus::Paid)
            ->where('updated_at', '>=', $windowStart)
            ->selectRaw($this->monthExpression('updated_at').' as period, SUM(total) as aggregate')
            ->groupBy('period')
            ->pluck('aggregate', 'period');

        $bookedData = $months
            ->map(fn (Carbon $month) => (float) ($bookedByMonth[$month->format('Y-m')] ?? 0))
            ->all();

        $collectedData = $months
            ->map(fn (Carbon $month) => (float) ($collectedByMonth[$month->format('Y-m')] ?? 0))
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Booked Revenue',
                    'data' => $bookedData,
                    'borderColor' => '#0ea5e9',
                ],
                [
                    'label' => 'Collected Revenue',
                    'data' => $collectedData,
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
