<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Enums\ProjectStatus;
use App\Models\Lead;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessSnapshot extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    protected function getStats(): array
    {
        $weekStart = now()->startOfWeek();

        $newThisWeek = Lead::query()->where('created_at', '>=', $weekStart)->count();
        $newToday = Lead::query()->whereDate('created_at', now()->toDateString())->count();

        $pipelineValue = Lead::query()
            ->where('status', LeadStatus::Qualified)
            ->selectRaw('SUM((calculated_estimate_low + calculated_estimate_high) / 2) as total')
            ->value('total');

        $qualifiedCount = Lead::query()->where('status', LeadStatus::Qualified)->count();

        $activeProjects = Project::query()->where('status', ProjectStatus::Active)->count();
        $scheduledProjects = Project::query()->where('status', ProjectStatus::Scheduled)->count();

        $monthBookedValue = Project::query()
            ->where('started_at', '>=', now()->startOfMonth())
            ->sum('contract_value');

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
