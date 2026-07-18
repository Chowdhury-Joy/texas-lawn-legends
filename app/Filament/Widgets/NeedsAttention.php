<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class NeedsAttention extends TableWidget
{
    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Needs Your Attention';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()
                    ->whereNotNull('escalated_at')
                    ->where('status', LeadStatus::Qualified)
                    ->orderBy('escalated_at')
            )
            ->emptyStateHeading('Nothing stalled right now')
            ->emptyStateDescription('Cold quotes that need a personal follow-up call will show up here.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->copyable(),
                TextColumn::make('neighborhood')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('service_type')
                    ->placeholder('—'),
                TextColumn::make('calculated_estimate_low')
                    ->money('usd')
                    ->label('Est. Low'),
                TextColumn::make('calculated_estimate_high')
                    ->money('usd')
                    ->label('Est. High'),
                TextColumn::make('escalated_at')
                    ->label('Stalled since')
                    ->since()
                    ->color('danger')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
