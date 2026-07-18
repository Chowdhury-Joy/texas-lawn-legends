<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Machine key, e.g. site_name or primary_phone.'),
                Select::make('type')
                    ->required()
                    ->native(false)
                    ->default('string')
                    ->options([
                        'string' => 'String',
                        'text' => 'Text',
                        'integer' => 'Integer',
                        'decimal' => 'Decimal',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ]),
                TextInput::make('group')
                    ->required()
                    ->maxLength(255)
                    ->default('general'),
                Textarea::make('value')
                    ->rows(4)
                    ->columnSpanFull()
                    ->helperText('For JSON types, enter a valid JSON array or object.'),
            ]);
    }
}
