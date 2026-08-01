<?php

namespace App\Filament\Pages;

use App\Support\PageBlocks;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
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
                        ->helperText('Square PNG/ICO shown in the browser tab. Leave empty to use the icon that ships with the active industry pack.')
                        ->columnSpanFull(),
                ]),
            Section::make('Color Tokens')
                ->description('Primary and accent fills are backgrounds — label ink flips automatically to near-black or near-white. Structural slate is body text on white and must stay dark.')
                ->columns(2)
                ->schema([
                    ColorPicker::make('color_primary')
                        ->label('Primary (forest green)')
                        ->helperText('Dark section bands (e.g. Projects).'),
                    ColorPicker::make('color_primary_light')
                        ->label('Primary light')
                        ->helperText('Cards and raised panels on primary sections.'),
                    ColorPicker::make('color_accent')
                        ->label('Accent (safety yellow)')
                        ->helperText('Primary button / highlight fill. Label text flips automatically to near-black or near-white.'),
                    ColorPicker::make('color_slate')
                        ->label('Structural slate')
                        ->helperText('Used for secondary body text (e.g. captions). Must contrast on white — light picks fall back to default slate (#334155).'),
                ]),
        ];
    }
}
