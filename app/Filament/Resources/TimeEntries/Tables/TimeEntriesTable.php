<?php

namespace App\Filament\Resources\TimeEntries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TimeEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('crew.name')
                    ->label('Crew')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('project.project_title')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('clock_in_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('clock_out_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('hourly_rate')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('calculated_cost')
                    ->money('usd')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('crew_id')
                    ->relationship('crew', 'name')
                    ->label('Crew'),
                SelectFilter::make('project_id')
                    ->relationship('project', 'project_title')
                    ->label('Project'),
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
