<?php

namespace App\Filament\Resources\TimeEntries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TimeEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('crew_id')
                    ->relationship('crew', 'name')
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Crew $record) => $record->name ?: "Crew #{$record->id}")
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('project_id')
                    ->relationship('project', 'project_title')
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Project $record) => $record->project_title ?: "Project #{$record->id}")
                    ->required()
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('clock_in_at')
                    ->required()
                    ->default(now()),
                DateTimePicker::make('clock_out_at'),
                TextInput::make('hourly_rate')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->default(0),
            ]);
    }
}
