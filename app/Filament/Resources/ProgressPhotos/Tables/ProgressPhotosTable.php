<?php

namespace App\Filament\Resources\ProgressPhotos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProgressPhotosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Photo')
                    ->square()
                    ->height(56),
                TextColumn::make('caption')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('project.project_title')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('milestone_step')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('project')
                    ->relationship('project', 'project_title')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
