<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('author')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('neighborhood')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('service_tag')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('review_text')
                    ->limit(50)
                    ->wrap(),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_featured'),
                SelectFilter::make('rating')
                    ->options([
                        5 => '5 stars',
                        4 => '4 stars',
                        3 => '3 stars',
                        2 => '2 stars',
                        1 => '1 star',
                    ]),
                SelectFilter::make('neighborhood')
                    ->options(fn () => Testimonial::query()
                        ->whereNotNull('neighborhood')
                        ->where('neighborhood', '!=', '')
                        ->distinct()
                        ->pluck('neighborhood', 'neighborhood')
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
