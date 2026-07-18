<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageSeo extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'SEO & Analytics';

    protected static ?string $title = 'SEO & Analytics';

    protected function settingsGroup(): string
    {
        return 'seo';
    }

    protected function settingsMap(): array
    {
        return [
            'meta_title' => 'string',
            'meta_description' => 'text',
            'meta_keywords' => 'string',
            'og_image' => 'string',
            'google_analytics_id' => 'string',
            'google_tag_manager_id' => 'string',
            'robots_index' => 'boolean',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Search Engine Metadata')
                ->schema([
                    TextInput::make('meta_title')
                        ->label('Default meta title')
                        ->required()
                        ->maxLength(70)
                        ->helperText('Recommended under 60 characters.'),
                    Textarea::make('meta_description')
                        ->label('Default meta description')
                        ->rows(3)
                        ->maxLength(180)
                        ->helperText('Recommended under 160 characters.'),
                    TextInput::make('meta_keywords')
                        ->label('Meta keywords')
                        ->maxLength(255),
                ]),
            Section::make('Social Sharing')
                ->schema([
                    FileUpload::make('og_image')
                        ->label('Social share image (Open Graph)')
                        ->image()
                        ->directory('seo')
                        ->disk('public')
                        ->visibility('public')
                        ->helperText('Shown when the site is shared on social media. 1200×630 recommended.'),
                ]),
            Section::make('Analytics & Indexing')
                ->columns(2)
                ->schema([
                    TextInput::make('google_analytics_id')
                        ->label('Google Analytics ID')
                        ->placeholder('G-XXXXXXXXXX')
                        ->maxLength(50),
                    TextInput::make('google_tag_manager_id')
                        ->label('Google Tag Manager ID')
                        ->placeholder('GTM-XXXXXXX')
                        ->maxLength(50),
                    Toggle::make('robots_index')
                        ->label('Allow search engines to index this site')
                        ->helperText('Turn off to emit a noindex directive site-wide.')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
