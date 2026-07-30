<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Filament\Concerns\RestrictedWidget;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadFunnel extends ChartWidget
{
    use RestrictedWidget;

    protected ?string $heading = 'Lead Funnel — Last 30 Days';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $since = now()->subDays(30);

        $statuses = LeadStatus::cases();

        // One grouped query instead of a count per status.
        $countsByStatus = Lead::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $counts = collect($statuses)->map(
            fn (LeadStatus $status) => (int) ($countsByStatus[$status->value] ?? 0)
        );

        $colors = [
            'partial' => '#94a3b8',
            'qualified' => '#facc15',
            'contacted' => '#38bdf8',
            'booked' => '#1b4332',
            'lost' => '#dc2626',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $counts->values()->all(),
                    'backgroundColor' => collect($statuses)->map(fn (LeadStatus $status) => $colors[$status->value] ?? '#94a3b8')->all(),
                ],
            ],
            'labels' => collect($statuses)->map(fn (LeadStatus $status) => $status->getLabel())->all(),
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
