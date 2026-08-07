<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Enums\ProjectStatus;
use App\Filament\Concerns\RestrictedWidget;
use App\Models\Lead;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessSnapshot extends StatsOverviewWidget
{
    use RestrictedWidget;

    /** Above-the-fold headline numbers, and now two queries — render inline. */
    protected static bool $isLazy = false;

    protected static ?int $sort = -3;

    protected function getStats(): array
    {
        $weekStart = now()->startOfWeek();

        // Week/today counts, qualified pipeline, and qualified count in one pass.
        $leadTotals = Lead::query()
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_this_week', [$weekStart])
            ->selectRaw('SUM(CASE WHEN date(created_at) = ? THEN 1 ELSE 0 END) as new_today', [now()->toDateString()])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as qualified_count', [LeadStatus::Qualified->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN (calculated_estimate_low + calculated_estimate_high) / 2 ELSE 0 END) as pipeline_value', [LeadStatus::Qualified->value])
            ->first();

        $newThisWeek = (int) ($leadTotals->new_this_week ?? 0);
        $newToday = (int) ($leadTotals->new_today ?? 0);
        $qualifiedCount = (int) ($leadTotals->qualified_count ?? 0);
        $pipelineValue = $leadTotals->pipeline_value ?? 0;

        $projectTotals = Project::query()
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active_count', [ProjectStatus::Active->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as scheduled_count', [ProjectStatus::Scheduled->value])
            ->selectRaw('SUM(CASE WHEN started_at >= ? THEN contract_value ELSE 0 END) as month_booked_value', [now()->startOfMonth()])
            ->first();

        $activeProjects = (int) ($projectTotals->active_count ?? 0);
        $scheduledProjects = (int) ($projectTotals->scheduled_count ?? 0);
        $monthBookedValue = $projectTotals->month_booked_value ?? 0;

        return [
            Stat::make('New Leads This Week', (string) $newThisWeek)
                ->description("{$newToday} today")
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('info'),

            Stat::make('Pipeline Value', '$'.number_format((float) ($pipelineValue ?? 0)))
                ->description("{$qualifiedCount} quote".($qualifiedCount === 1 ? '' : 's').' awaiting booking')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),

            Stat::make('Active Projects', (string) $activeProjects)
                ->description("{$scheduledProjects} scheduled to start")
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make("This Month's Booked Value", '$'.number_format((float) $monthBookedValue))
                ->description('Contract value of jobs started this month')
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),
        ];
    }
}
