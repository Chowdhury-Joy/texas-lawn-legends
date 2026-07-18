<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageHomepage extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Homepage Content';

    protected static ?string $title = 'Homepage Content';

    protected function settingsGroup(): string
    {
        return 'homepage';
    }

    protected function settingsMap(): array
    {
        return [
            'hero_eyebrow' => 'string',
            'hero_heading' => 'string',
            'hero_subheading' => 'text',
            'hero_cta_primary_label' => 'string',
            'hero_cta_secondary_label' => 'string',
            'hero_media_image' => 'string',
            'hero_media_badge' => 'string',
            'hero_media_neighborhood' => 'string',
            'hero_media_title' => 'string',
            'hero_media_subtitle' => 'string',
            'trust_badges' => 'json',
            'process_heading' => 'string',
            'process_steps' => 'json',
            'create_suite_heading' => 'string',
            'care_suite_heading' => 'string',
            'proof_heading' => 'string',
            'proof_subheading' => 'string',
            'cta_heading' => 'string',
            'cta_subheading' => 'text',
            'cta_button_label' => 'string',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Hero')
                ->columns(2)
                ->schema([
                    TextInput::make('hero_eyebrow')->label('Eyebrow tag')->columnSpanFull(),
                    TextInput::make('hero_heading')->label('Headline')->required()->columnSpanFull(),
                    Textarea::make('hero_subheading')->label('Sub-heading')->rows(3)->columnSpanFull(),
                    TextInput::make('hero_cta_primary_label')->label('Primary button label'),
                    TextInput::make('hero_cta_secondary_label')->label('Secondary button label (phone appended)'),
                ]),
            Section::make('Hero Media Card')
                ->columns(2)
                ->schema([
                    FileUpload::make('hero_media_image')
                        ->label('Hero image (optional)')
                        ->image()->directory('homepage')->disk('public')->visibility('public')
                        ->helperText('Falls back to the styled placeholder card when empty.')
                        ->columnSpanFull(),
                    TextInput::make('hero_media_badge')->label('Badge'),
                    TextInput::make('hero_media_neighborhood')->label('Neighborhood label'),
                    TextInput::make('hero_media_title')->label('Project title'),
                    TextInput::make('hero_media_subtitle')->label('Project subtitle'),
                ]),
            Section::make('Trust Bar')
                ->schema([
                    TagsInput::make('trust_badges')
                        ->label('Verification badges')
                        ->placeholder('Add a badge')
                        ->helperText('Each entry renders as a verification badge in the trust bar.'),
                ]),
            Section::make('3-Step Process')
                ->schema([
                    TextInput::make('process_heading')->label('Section heading'),
                    Repeater::make('process_steps')
                        ->label('Steps')
                        ->schema([
                            TextInput::make('number')->label('Number')->required()->maxLength(4),
                            TextInput::make('title')->label('Title')->required(),
                            Textarea::make('body')->label('Body')->rows(3)->required(),
                        ])
                        ->columns(1)
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => ($state['number'] ?? '').' — '.($state['title'] ?? '')),
                ]),
            Section::make('Service Suites')
                ->columns(2)
                ->schema([
                    TextInput::make('create_suite_heading')->label('Create Suite heading'),
                    TextInput::make('care_suite_heading')->label('Care Suite heading'),
                ]),
            Section::make('Neighborhood Proof')
                ->schema([
                    TextInput::make('proof_heading')->label('Heading'),
                    TextInput::make('proof_subheading')->label('Sub-heading'),
                ]),
            Section::make('Closing CTA')
                ->schema([
                    TextInput::make('cta_heading')->label('Heading'),
                    Textarea::make('cta_subheading')->label('Sub-heading')->rows(2),
                    TextInput::make('cta_button_label')->label('Button label'),
                ]),
        ];
    }
}
