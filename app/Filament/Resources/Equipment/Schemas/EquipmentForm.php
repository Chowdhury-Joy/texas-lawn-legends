<?php

namespace App\Filament\Resources\Equipment\Schemas;

use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asset Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->options(EquipmentType::class)
                            ->required()
                            ->native(false),
                        Select::make('status')
                            ->options(EquipmentStatus::class)
                            ->required()
                            ->native(false)
                            ->default(EquipmentStatus::Active->value),
                        Select::make('crew_id')
                            ->relationship('crew', 'name')
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Crew $record) => $record->name ?: "Crew #{$record->id}")
                            ->searchable()
                            ->preload()
                            ->label('Assigned Crew'),
                        DatePicker::make('purchase_date'),
                        DatePicker::make('next_maintenance_at')
                            ->label('Next Maintenance Date'),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
