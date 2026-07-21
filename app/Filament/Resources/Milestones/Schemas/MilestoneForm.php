<?php

namespace App\Filament\Resources\Milestones\Schemas;

use App\Enums\MilestoneStatus;
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
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Project $record) => $record->project_title ?: "Project #{$record->id}")
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
                    ->default(MilestoneStatus::Pending->value),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
