<?php

namespace App\Filament\Resources\Addons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class AddonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('base_price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01),
                TextInput::make('price_unit')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('per application')
                    ->helperText("e.g. 'per application' or 'per dynamic sqft'."),
                FileUpload::make('image_path')
                    ->image()
                    ->directory('addons')
                    ->visibility('public')
                    ->columnSpanFull(),
                Toggle::make('is_available')
                    ->default(true),
            ]);
    }
}
