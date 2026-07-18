<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingSiteVisits extends TableWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = "This Week's Site Visits";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()
                    ->where('status', LeadStatus::Booked)
                    ->whereBetween('scheduled_at', [now(), now()->endOfWeek()])
                    ->orderBy('scheduled_at')
            )
            ->emptyStateHeading('No visits booked this week yet')
            ->emptyStateIcon('heroicon-o-calendar')
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('When')
                    ->dateTime('D, M j — g:i A')
                    ->weight('bold')
                    ->color('primary'),
                TextColumn::make('name')
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->copyable(),
                TextColumn::make('neighborhood')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('service_type')
                    ->placeholder('—'),
                TextColumn::make('address')
                    ->placeholder('—')
                    ->limit(30),
            ])
            ->paginated(false);
    }
}
