<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivity extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Activity';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->whereIn('log_name', ['lead', 'project'])
                    ->latest()
            )
            ->emptyStateHeading('No activity yet')
            ->emptyStateDescription('Changes to leads and projects will appear here.')
            ->emptyStateIcon('heroicon-o-clock')
            ->columns([
                TextColumn::make('log_name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'project' ? 'success' : 'info'),
                TextColumn::make('description')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('subject_type')
                    ->label('Record')
                    ->formatStateUsing(fn (?string $state, $record): string => class_basename($state ?? '').' #'.$record->subject_id),
                TextColumn::make('causer.name')
                    ->label('By')
                    ->default('System'),
                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(10);
    }
}
