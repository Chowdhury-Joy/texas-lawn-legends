<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Chart';

    protected function getData(): array
    {
        $bookedData = [];
        $collectedData = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $labels[] = $date->format('M');

            $booked = Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('contract_value');
            $bookedData[] = $booked;

            $collected = Invoice::whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('total_amount');
            $collectedData[] = $collected;
        }

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
