<?php

namespace App\Filament\Concerns;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Shared configuration for a read-only Spatie Activitylog timeline
 * shown as a relation manager on a resource's edit page.
 */
trait ActivityLogRelationManager
{
    public function table(Table $table): Table
    {
        return $table
            ->heading('Activity log')
            ->defaultSort('created_at', 'desc')
            ->columns([
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
                TextColumn::make('causer.name')
                    ->label('By')
                    ->placeholder('System')
                    ->default('System'),
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('M j, Y g:i A')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema(fn (Schema $schema): Schema => $schema->components([
                        TextEntry::make('description')->label('Event')->badge(),
                        TextEntry::make('created_at')->label('When')->dateTime('M j, Y g:i A'),
                        KeyValueEntry::make('properties.old')
                            ->label('Old values')
                            ->state(fn ($record) => $record->properties['old'] ?? [])
                            ->columnSpanFull(),
                        KeyValueEntry::make('properties.attributes')
                            ->label('New values')
                            ->state(fn ($record) => $record->properties['attributes'] ?? [])
                            ->columnSpanFull(),
                    ])),
            ])
            ->headerActions([])
            ->toolbarActions([]);
    }
}
