<?php

namespace App\Filament\Resources\Milestones\Schemas;

use App\Enums\MilestoneStatus;
use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'project_title')
                    ->getOptionLabelFromRecordUsing(fn (Project $record) => $record->project_title ?: "Project #{$record->id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options(MilestoneStatus::class)
                    ->required()
                    ->native(false)
                    ->live() // so the completed_at field below shows/hides immediately
                    ->default(MilestoneStatus::Pending->value),
                DateTimePicker::make('completed_at')
                    ->label('Completed on')
                    ->native(false)
                    ->seconds(false)
                    // The model fills this automatically when status flips to
                    // Completed and clears it when a step is reopened; this
                    // field is only for correcting the date after the fact.
                    ->helperText('Set automatically when the step is marked Completed. Override only to correct the date shown on the client timeline.')
                    ->visible(fn ($get) => $get('status') === MilestoneStatus::Completed->value),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
