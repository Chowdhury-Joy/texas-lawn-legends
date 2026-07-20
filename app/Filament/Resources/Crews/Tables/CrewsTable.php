<?php

namespace App\Filament\Resources\Crews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CrewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Crew Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('leader_name')
                    ->label('Foreman / Leader')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Contact Phone')
                    ->placeholder('—'),

                TextColumn::make('color')
                    ->label('Badge Color')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'emerald' => 'success',
                        'amber' => 'warning',
                        'sky' => 'info',
                        'purple' => 'primary',
                        'rose' => 'danger',
                        'slate' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('projects_count')
                    ->label('Assigned Jobs')
                    ->counts('projects')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('name', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
