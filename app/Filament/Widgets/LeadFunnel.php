<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadFunnel extends ChartWidget
{
    protected static bool $isLazy = false;

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

        $counts = collect($statuses)->map(
            fn (LeadStatus $status) => Lead::query()
                ->where('status', $status)
                ->where('created_at', '>=', $since)
                ->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $counts->values()->all(),
                    'backgroundColor' => ['#94a3b8', '#facc15', '#1b4332', '#dc2626'],
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
