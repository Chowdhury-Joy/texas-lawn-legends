<?php

namespace App\Filament\Resources\AccessCodes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AccessCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => Str::upper(Str::random(8)))
                    ->helperText('Token the client enters to unlock the member portal.'),
                TextInput::make('target_month')
                    ->required()
                    ->maxLength(7)
                    ->placeholder('YYYY-MM')
                    ->default(fn () => now()->format('Y-m'))
                    ->rule('regex:/^\d{4}-\d{2}$/')
                    ->helperText('Month this code unlocks, formatted YYYY-MM.'),
                TextInput::make('client_id_restriction')
                    ->numeric()
                    ->label('Restrict to client ID')
                    ->helperText('Optional. Leave blank for any client.'),
                TextInput::make('usage_count')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
