<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('project_title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->client_name),
                TextColumn::make('neighborhood')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('contract_value')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('crew.name')
                    ->label('Crew')
                    ->placeholder('Unassigned')
                    ->badge()
                    ->color(fn ($record) => match ($record->crew?->color) {
                        'emerald' => 'success',
                        'amber' => 'warning',
                        'sky' => 'info',
                        'purple' => 'primary',
                        'rose' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('milestones_count')
                    ->counts('milestones')
                    ->label('Milestones')
                    ->badge()
                    ->color('info'),
                TextColumn::make('started_at')
                    ->date('M j, Y')
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->date('M j, Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProjectStatus::class),
                SelectFilter::make('crew_id')
                    ->relationship('crew', 'name')
                    ->label('Crew'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('viewDashboard')
                    ->label('View')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn ($record) => route('dashboard', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
