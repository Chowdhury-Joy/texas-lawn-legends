<?php

namespace App\Filament\Resources\ProgressPhotos\Schemas;

use App\Models\Milestone;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProgressPhotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'project_title')
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Project $record) => $record->project_title ?: "Project #{$record->id}")
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                FileUpload::make('image_path')
                    ->image()
                    ->directory('progress-photos')
                    ->visibility('public')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->maxLength(255),
                Select::make('milestone_step')
                    ->options(fn (Get $get) => Milestone::where('project_id', $get('project_id'))->whereNotNull('title')->pluck('title', 'title')->toArray())
                    ->searchable()
                    ->helperText('Label linking this photo to a milestone stage.'),
            ]);
    }
}
