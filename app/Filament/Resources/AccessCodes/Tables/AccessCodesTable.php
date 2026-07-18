<?php

namespace App\Filament\Resources\AccessCodes\Tables;

use App\Models\AccessCode;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AccessCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('target_month', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->fontFamily('mono'),
                TextColumn::make('target_month')
                    ->badge()
                    ->sortable(),
                TextColumn::make('client_id_restriction')
                    ->label('Client restriction')
                    ->placeholder('Any')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('usage_count')
                    ->numeric()
                    ->sortable()
                    ->label('Uses'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                SelectFilter::make('target_month')
                    ->options(fn () => AccessCode::query()
                        ->distinct()
                        ->orderByDesc('target_month')
                        ->pluck('target_month', 'target_month')
                        ->toArray()),
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
