<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgressPhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'progressPhotos';

    protected static ?string $recordTitleAttribute = 'caption';

    protected static ?string $title = 'Progress Photos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->image()
                    ->directory('progress-photos')
                    ->visibility('public')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->maxLength(255),
                TextInput::make('milestone_step')
                    ->maxLength(255)
                    ->helperText('Label linking this photo to a milestone stage.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Photo')
                    ->square(),
                TextColumn::make('caption')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('milestone_step')
                    ->label('Stage')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
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
