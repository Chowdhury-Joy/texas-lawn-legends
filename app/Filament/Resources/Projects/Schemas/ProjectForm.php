<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                        Select::make('crew_id')
                            ->relationship('crew', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Assigned Field Crew')
                            ->helperText('Assign a field crew responsible for this job.'),
                        TextInput::make('contract_value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->step(0.01),
                    ]),
                Section::make('Job Costing & Financials')
                    ->columns(2)
                    ->schema([
                        TextInput::make('material_cost')
                            ->label('Material Cost ($)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->default(0)
                            ->step(0.01)
                            ->helperText('Sod, flagstone, soil, lumber, plants, etc.'),
                        Toggle::make('use_manual_labor_cost')
                            ->label('Override Automated Labor Cost')
                            ->helperText('If enabled, time tracking entries will NOT update the labor cost automatically.')
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('labor_cost')
                            ->label('Labor Cost ($)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->default(0)
                            ->step(0.01)
                            ->disabled(fn (Get $get) => ! $get('use_manual_labor_cost'))
                            ->dehydrated()
                            ->helperText('Crew payroll & field labor cost for this job.'),
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
