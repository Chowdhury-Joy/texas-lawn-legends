<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\RestrictedWidget;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WeeklyLeadTrend extends ChartWidget
{
    use RestrictedWidget;

    protected ?string $heading = 'New Leads — Last 7 Days';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn (int $ago) => now()->subDays($ago)->startOfDay());

        // One grouped query for the window instead of a count per day.
        $countsByDay = Lead::query()
            ->where('created_at', '>=', $days->first())
            ->selectRaw('date(created_at) as day, count(*) as aggregate')
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $counts = $days->map(fn (Carbon $day) => (int) ($countsByDay[$day->toDateString()] ?? 0));

        return [
            'datasets' => [
                [
                    'label' => 'New leads',
                    'data' => $counts->values()->all(),
                    'borderColor' => '#1b4332',
                    'backgroundColor' => 'rgba(27, 67, 50, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days->map(fn (Carbon $day) => $day->format('D j'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
            ],
        ];
    }
}
