<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageContact extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Contact & Footer';

    protected static ?string $title = 'Contact & Footer';

    protected function settingsGroup(): string
    {
        return 'general';
    }

    protected function settingsMap(): array
    {
        return [
            'site_name' => 'string',
            'primary_phone' => 'string',
            'primary_email' => 'string',
            'business_address' => 'string',
            'service_areas' => 'json',
            'footer_note' => 'string',
            'footer_copyright' => 'text',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Business Details')
                ->columns(2)
                ->schema([
                    TextInput::make('site_name')
                        ->label('Site / business name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('primary_phone')
                        ->label('Primary phone')
                        ->tel()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('primary_email')
                        ->label('Primary email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('business_address')
                        ->label('Business address')
                        ->maxLength(255),
                    TagsInput::make('service_areas')
                        ->label('Service areas / neighborhoods')
                        ->placeholder('Add a neighborhood')
                        ->helperText('Used across the site and in local SEO structured data.')
                        ->columnSpanFull(),
                ]),
            Section::make('Footer')
                ->schema([
                    TextInput::make('footer_note')
                        ->label('Footer note')
                        ->maxLength(255),
                    Textarea::make('footer_copyright')
                        ->label('Copyright line')
                        ->rows(2),
                ]),
        ];
    }
}
