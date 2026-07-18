<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Enums\ServiceCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('URL-friendly identifier. Auto-generated from the title.'),
                Select::make('category')
                    ->options(ServiceCategory::class)
                    ->required()
                    ->native(false),
                TextInput::make('icon')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Heroicon name or SVG asset key, e.g. heroicon-o-home.'),
                Textarea::make('short_description')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('long_description')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                TextInput::make('base_price_multiplier')
                    ->required()
                    ->numeric()
                    ->default(1.00)
                    ->step(0.01)
                    ->helperText('Multiplier applied to the base per-sqft rate for this service.'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
