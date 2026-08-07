<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadConversionStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalLeads = Lead::whereIn('status', [
            LeadStatus::Qualified,
            LeadStatus::Contacted,
            LeadStatus::Booked,
        ])->count();
        $convertedLeads = Project::whereNotNull('lead_id')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        // One grouped query for the 7-day sparkline instead of a count per day.
        $trendStart = now()->subDays(6)->startOfDay();
        $convertedByDay = Project::whereNotNull('lead_id')
            ->where('created_at', '>=', $trendStart)
            ->selectRaw('date(created_at) as day, count(*) as aggregate')
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $trend[] = (int) ($convertedByDay[$day] ?? 0);
        }

        return [
            Stat::make('Lead Conversion Rate', $conversionRate.'%')
                ->description($convertedLeads.' out of '.$totalLeads.' qualified leads converted')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($trend),
        ];
    }
}
