<?php

namespace App\Filament\Pages;

use App\Support\PageBlocks;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageBranding extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Branding & Theme';

    protected static ?string $title = 'Branding & Theme';

    protected function settingsGroup(): string
    {
        return 'branding';
    }

    protected function settingsMap(): array
    {
        return [
            'logo_image' => 'string',
            'logo_text' => 'string',
            'logo_badge' => 'string',
            'favicon' => 'string',
            'brand_font' => 'string',
            'color_primary' => 'string',
            'color_primary_light' => 'string',
            'color_accent' => 'string',
            'color_slate' => 'string',
            'theme' => 'string',

        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Theme')
                ->schema([
                    Select::make('theme')
                        ->label('Site theme')
                        ->native(false)
                        ->default('clean')
                        ->options(PageBlocks::themes())
                        ->helperText('Clean is the default. Bold reproduces the original hard-edged brutalist look; the others are softer stylistic variants.'),
                ]),
            Section::make('Logo & Identity')
                ->columns(2)
                ->schema([
                    FileUpload::make('logo_image')
                        ->label('Logo image')
                        ->image()
                        ->directory('branding')
                        ->disk('public')
                        ->visibility('public')
                        ->imageEditor()
                        ->helperText('Optional. Falls back to the wordmark text below when empty.')
                        ->columnSpanFull(),
                    TextInput::make('logo_text')
                        ->label('Wordmark text')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('logo_badge')
                        ->label('Badge / tagline')
                        ->maxLength(255)
                        ->helperText('Small badge under the logo, e.g. "EST. 2019 | Dallas, TX".'),
                    FileUpload::make('favicon')
                        ->label('Favicon')
                        ->image()
                        ->directory('branding')
                        ->disk('public')
                        ->visibility('public')
                        ->helperText('Square PNG/ICO shown in the browser tab.')
                        ->columnSpanFull(),
                ]),
            Section::make('Typography')
                ->schema([
                    Select::make('brand_font')
                        ->label('Brand font')
                        ->native(false)
                        ->options([
                            'Montserrat' => 'Montserrat (default)',
                            'Inter' => 'Inter',
                            'Poppins' => 'Poppins',
                            'Oswald' => 'Oswald',
                            'Roboto' => 'Roboto',
                            'Work Sans' => 'Work Sans',
                            'Archivo' => 'Archivo',
                            'Barlow' => 'Barlow',
                        ])
                        ->helperText('Non-default fonts load from the Bunny Fonts CDN at runtime.'),
                ]),
            Section::make('Color Tokens')
                ->columns(2)
                ->schema([
                    ColorPicker::make('color_primary')
                        ->label('Primary (forest green)'),
                    ColorPicker::make('color_primary_light')
                        ->label('Primary light'),
                    ColorPicker::make('color_accent')
                        ->label('Accent (safety yellow)'),
                    ColorPicker::make('color_slate')
                        ->label('Structural slate'),
                ]),
            Section::make('Theme Preview')
                ->description('Preview each theme live without saving. Select a theme above, then Save changes to apply it.')
                ->schema([
                    Placeholder::make('theme_preview')
                        ->label('')
                        ->content(view('filament.pages.theme-preview')),
                ]),
        ];
    }
}
