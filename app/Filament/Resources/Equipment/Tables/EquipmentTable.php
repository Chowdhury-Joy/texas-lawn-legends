<?php

namespace App\Filament\Resources\Equipment\Tables;

use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('crew.name')
                    ->label('Assigned Crew')
                    ->sortable()
                    ->placeholder('Unassigned'),
                TextColumn::make('next_maintenance_at')
                    ->label('Next Maintenance')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'success'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(EquipmentType::class),
                SelectFilter::make('status')
                    ->options(EquipmentStatus::class),
                SelectFilter::make('crew_id')
                    ->relationship('crew', 'name')
                    ->label('Crew'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
