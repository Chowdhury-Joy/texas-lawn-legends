<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project')
                    ->columns(2)
                    ->schema([
                        TextInput::make('project_title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('client_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('neighborhood')
                            ->required()
                            ->maxLength(255),
                        Select::make('lead_id')
                            ->relationship('lead', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Linked lead')
                            ->helperText('Optionally connect this project to an existing lead.'),
                        TextInput::make('contract_value')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                    ]),
                Section::make('Status & Timeline')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->options(ProjectStatus::class)
                            ->required()
                            ->native(false)
                            ->default(ProjectStatus::Scheduled->value),
                        DatePicker::make('started_at')->required(),
                        DatePicker::make('completed_at'),
                        TextInput::make('unique_dashboard_hash')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated on save')
                            ->helperText(fn (?Project $record) => $record
                                ? url('/dashboard/'.$record->unique_dashboard_hash)
                                : 'The private client dashboard URL is generated automatically.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
