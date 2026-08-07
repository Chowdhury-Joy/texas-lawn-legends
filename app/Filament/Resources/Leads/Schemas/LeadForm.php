<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->maxLength(255),
                        TextInput::make('email')->email()->maxLength(255),
                        TextInput::make('phone')->tel()->maxLength(255),
                        TextInput::make('neighborhood')->maxLength(255),
                        TextInput::make('address')->maxLength(255)->columnSpanFull(),
                    ]),
                Section::make('Estimate')
                    ->columns(2)
                    ->schema([
                        TextInput::make('service_type')->maxLength(255),
                        TextInput::make('estimated_sqft')->numeric()->suffix(fn () => niche_label('size_unit')),
                        TextInput::make('calculated_estimate_low')
                            ->numeric()->prefix('$')->step(0.01),
                        TextInput::make('calculated_estimate_high')
                            ->numeric()->prefix('$')->step(0.01),
                    ]),
                Section::make('Pipeline')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->options(LeadStatus::class)
                            ->required()
                            ->native(false)
                            ->default(LeadStatus::Partial->value),
                        TextInput::make('step_reached')->maxLength(255),
                        DateTimePicker::make('scheduled_at')->seconds(false),
                        TextInput::make('external_booking_id')->maxLength(255),
                    ]),
            ]);
    }
}
