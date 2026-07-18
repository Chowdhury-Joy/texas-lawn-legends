<?php

namespace App\Filament\Resources\ProgressPhotos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ProgressPhotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'project_title')
                    ->searchable()
                    ->preload()
                    ->required(),
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
}
