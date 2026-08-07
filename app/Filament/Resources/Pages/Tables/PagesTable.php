<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->formatStateUsing(fn (?string $state): string => '/'.$state)
                    ->searchable()
                    ->url(fn ($record) => url($record->slug))
                    ->openUrlInNewTab(),
                IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Published'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->visible(fn ($record) => (bool) $record->is_published)
                    ->url(fn ($record) => url($record->slug))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->action(fn (Collection $records) => static::setPublished($records, true))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('unpublish')
                        ->label('Unpublish')
                        ->icon('heroicon-m-eye-slash')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Unpublish pages?')
                        ->modalDescription('Selected pages will stop being publicly visible immediately.')
                        ->action(fn (Collection $records) => static::setPublished($records, false))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function setPublished(Collection $records, bool $published): void
    {
        $records->each(fn ($record) => $record->update(['is_published' => $published]));

        Notification::make()
            ->title($published ? 'Pages published' : 'Pages unpublished')
            ->success()
            ->send();
    }
}
