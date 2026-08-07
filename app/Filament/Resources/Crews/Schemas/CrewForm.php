<?php

namespace App\Filament\Resources\Crews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CrewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Crew Details')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Crew Name')
                            ->placeholder('e.g. Crew Alpha — Hardscaping')
                            ->required()
                            ->columnSpan(2),

                        TextInput::make('leader_name')
                            ->label('Crew Leader / Foreman')
                            ->placeholder('e.g. Carlos Mendez'),

                        TextInput::make('phone')
                            ->label('Crew Contact Phone')
                            ->tel()
                            ->placeholder('e.g. (214) 555-0192'),

                        Select::make('color')
                            ->label('Schedule Badge Color')
                            ->options([
                                'emerald' => 'Emerald Green',
                                'amber' => 'Amber Gold',
                                'sky' => 'Sky Blue',
                                'purple' => 'Violet Purple',
                                'rose' => 'Crimson Rose',
                                'slate' => 'Slate Gray',
                            ])
                            ->default('emerald')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Notes & Equipment Assignments')
                            ->placeholder('Assigned to Truck #3, Trailer B. Specializes in heavy stone paving.')
                            ->rows(3)
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
