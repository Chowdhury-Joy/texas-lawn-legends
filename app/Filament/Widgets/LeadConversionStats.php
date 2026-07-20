<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadConversionStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalLeads = \App\Models\Lead::whereIn('status', [
            \App\Enums\LeadStatus::Qualified,
            \App\Enums\LeadStatus::Contacted,
            \App\Enums\LeadStatus::Booked,
        ])->count();
        $convertedLeads = \App\Models\Project::whereNotNull('lead_id')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $trend[] = \App\Models\Project::whereNotNull('lead_id')
                ->whereDate('created_at', now()->subDays($i))
                ->count();
        }

        return [
            Stat::make('Lead Conversion Rate', $conversionRate . '%')
                ->description($convertedLeads . ' out of ' . $totalLeads . ' qualified leads converted')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($trend),
        ];
    }
}
