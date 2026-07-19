<?php

namespace App\Support;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use InvalidArgumentException;

/**
 * Single source of truth for the page builder's block types: labels for the
 * GrapesJS palette, and the Filament field schema each type's side-panel form uses.
 */
class PageBlocks
{
    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'hero' => 'Hero',
            'trust_bar' => 'Trust Bar',
            'three_step' => '3-Step Process',
            'service_matrix' => 'Service Matrix (Create/Care)',
            'neighborhood_proof' => 'Neighborhood Proof (testimonials)',
            'cta_banner' => 'CTA Banner',
            'rich_text' => 'Rich Text',
            'image_text_split' => 'Image + Text Split',
            'gallery' => 'Gallery',
            'faq' => 'FAQ',
            'about' => 'About Us',
            'stat_band' => 'Stat Band',
            'testimonial_quote' => 'Testimonial Quote',
            'icon_feature' => 'Icon Feature Grid',
            'logo_cloud' => 'Logo Cloud',
            'testimonial_grid' => 'Testimonial Grid',
            'review_spotlight' => 'Review Spotlight',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function forHomepage(): array
    {
        return static::all();
    }

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_keys(static::labels());
    }

    /**
     * @return array<int, mixed>
     */
    public static function fields(string $type): array
    {
        return match ($type) {
            'hero' => static::hero(),
            'trust_bar' => static::trustBar(),
            'three_step' => static::threeStep(),
            'service_matrix' => static::serviceMatrix(),
            'neighborhood_proof' => static::neighborhoodProof(),
            'cta_banner' => static::ctaBanner(),
            'rich_text' => static::richText(),
            'image_text_split' => static::imageTextSplit(),
            'gallery' => static::gallery(),
            'faq' => static::faq(),
            'about' => static::about(),
            'stat_band' => static::statBand(),
            'testimonial_quote' => static::testimonialQuote(),
            'icon_feature' => static::iconFeature(),
            'logo_cloud' => static::logoCloud(),
            'testimonial_grid' => static::testimonialGrid(),
            'review_spotlight' => static::reviewSpotlight(),
            default => throw new InvalidArgumentException("Unknown block type [$type]."),
        };
    }

    private static function hero(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            TextInput::make('heading')->label('Headline')->required()->columnSpanFull(),
            Textarea::make('subheading')->label('Sub-heading')->rows(3)->columnSpanFull(),
            TextInput::make('cta_primary_label')->label('Primary button label'),
            TextInput::make('cta_secondary_label')->label('Secondary button label (phone appended)'),
            FileUpload::make('media_image')
                ->label('Hero image (optional)')
                ->image()->directory('homepage')->disk('public')->visibility('public')
                ->imageEditor()->imageEditorAspectRatios([16 / 9, 21 / 9])
                ->helperText('Falls back to the default desktop image when empty.')
                ->columnSpanFull(),
            FileUpload::make('media_image_mobile')
                ->label('Hero mobile image (optional)')
                ->image()->directory('homepage')->disk('public')->visibility('public')
                ->imageEditor()->imageEditorAspectRatios([1, 4 / 5])
                ->helperText('Used below 632px. Falls back to the default mobile image when empty.')
                ->columnSpanFull(),
            TextInput::make('media_badge')->label('Media badge'),
            TextInput::make('media_neighborhood')->label('Media neighborhood label'),
            TextInput::make('media_title')->label('Media project title'),
            TextInput::make('media_subtitle')->label('Media project subtitle'),
        ];
    }

    private static function trustBar(): array
    {
        return [
            TagsInput::make('badges')
                ->label('Verification badges')
                ->placeholder('Add a badge')
                ->helperText('Each entry renders as a verification badge in the trust bar.'),
        ];
    }

    private static function threeStep(): array
    {
        return [
            TextInput::make('heading')->label('Section heading'),
            Repeater::make('steps')
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
            ...static::layout(),
        ];
    }

    private static function serviceMatrix(): array
    {
        return [
            TextInput::make('create_suite_heading')->label('Create Suite heading'),
            TextInput::make('care_suite_heading')->label('Care Suite heading'),
            ...static::layout(),
        ];
    }

    private static function neighborhoodProof(): array
    {
        return [
            TextInput::make('heading')->label('Heading'),
            TextInput::make('subheading')->label('Sub-heading'),
            ...static::layout(),
        ];
    }

    private static function ctaBanner(): array
    {
        return [
            TextInput::make('heading')->label('Heading'),
            Textarea::make('subheading')->label('Sub-heading')->rows(2),
            TextInput::make('button_label')->label('Button label'),
            TextInput::make('button_url')->label('Button URL')->helperText('Defaults to /estimate when left blank.'),
        ];
    }

    private static function richText(): array
    {
        return [
            TextInput::make('heading')->label('Heading (optional)'),
            RichEditor::make('body')->label('Body')->required()->columnSpanFull(),
        ];
    }

    private static function imageTextSplit(): array
    {
        return [
            FileUpload::make('image')->label('Image')->image()->directory('pages')->disk('public')->visibility('public')->imageEditor()->imageEditorAspectRatios([1])->required(),
            TextInput::make('heading')->label('Heading'),
            Textarea::make('body')->label('Body')->rows(4),
            Toggle::make('reverse')->label('Reverse layout (image on right)'),
        ];
    }

    private static function gallery(): array
    {
        return [
            Repeater::make('images')
                ->label('Images')
                ->schema([
                    FileUpload::make('image')->label('Image')->image()->directory('pages')->disk('public')->visibility('public')->imageEditor()->imageEditorAspectRatios([4 / 3, 1])->required(),
                    TextInput::make('caption')->label('Caption'),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible(),
            ...static::layout(),
        ];
    }

    private static function faq(): array
    {
        return [
            TextInput::make('heading')->label('Heading'),
            Repeater::make('items')
                ->label('Questions')
                ->schema([
                    TextInput::make('question')->label('Question')->required(),
                    Textarea::make('answer')->label('Answer')->rows(3)->required(),
                ])
                ->columns(1)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
        ];
    }

    private static function about(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            FileUpload::make('image')
                ->label('Photo')
                ->image()->directory('pages')->disk('public')->visibility('public')
                ->imageEditor()->imageEditorAspectRatios([4 / 3, 1])
                ->helperText('Team, crew, or showcase yard photo.')
                ->columnSpanFull(),
            TextInput::make('heading')->label('Heading')->required()->columnSpanFull(),
            Textarea::make('body')->label('Body')->rows(4)->columnSpanFull(),
            Toggle::make('reverse')->label('Reverse layout (photo on right)'),
            Repeater::make('stats')
                ->label('Stat callouts')
                ->schema([
                    TextInput::make('value')->label('Value (e.g. 250+)')->required(),
                    TextInput::make('label')->label('Label')->required(),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => ($state['value'] ?? '').' — '.($state['label'] ?? '')),
            ...static::layout(),
        ];
    }

    private static function statBand(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            TextInput::make('heading')->label('Heading')->columnSpanFull(),
            Repeater::make('stats')
                ->label('Stat callouts')
                ->schema([
                    TextInput::make('value')->label('Value (e.g. 250+)')->required(),
                    TextInput::make('label')->label('Label')->required(),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => ($state['value'] ?? '').' — '.($state['label'] ?? '')),
            ...static::layout(),
        ];
    }

    private static function testimonialQuote(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            Textarea::make('quote')
                ->label('Quote')
                ->rows(3)
                ->required()
                ->columnSpanFull(),
            TextInput::make('author')->label('Author name'),
            TextInput::make('role')->label('Author role / neighborhood'),
            FileUpload::make('avatar')
                ->label('Avatar')
                ->avatar()
                ->image()->directory('pages')->disk('public')->visibility('public')
                ->imageEditor()->imageEditorAspectRatios([1])
                ->columnSpanFull(),
            ...static::layout(),
        ];
    }

    private static function iconFeature(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            TextInput::make('heading')->label('Heading')->columnSpanFull(),
            Repeater::make('features')
                ->label('Features')
                ->schema([
                    TextInput::make('icon')->label('Icon name (svg-icon)'),
                    TextInput::make('title')->label('Title')->required(),
                    Textarea::make('body')->label('Body')->rows(2),
                ])
                ->columns(1)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ...static::layout(),
        ];
    }

    private static function logoCloud(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            TextInput::make('heading')->label('Heading')->columnSpanFull(),
            Repeater::make('logos')
                ->label('Logos')
                ->schema([
                    FileUpload::make('image')->label('Logo')->image()->directory('pages')->disk('public')->visibility('public')->imageEditor()->imageEditorAspectRatios([3 / 2, 2 / 1])->required(),
                    TextInput::make('label')->label('Alt text / label'),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible(),
            ...static::layout(),
        ];
    }

    private static function testimonialGrid(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            TextInput::make('heading')->label('Heading')->columnSpanFull(),
            TextInput::make('subheading')->label('Sub-heading')->columnSpanFull(),
            Toggle::make('featured_only')
                ->label('Show featured reviews only')
                ->helperText('When on, only testimonials marked featured are shown.')
                ->columnSpanFull(),
            ...static::layout(),
        ];
    }

    private static function reviewSpotlight(): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow tag')->columnSpanFull(),
            TextInput::make('heading')->label('Heading')->columnSpanFull(),
            Toggle::make('random')
                ->label('Show a random review')
                ->helperText('When on, a random testimonial is featured; otherwise the first featured one.')
                ->columnSpanFull(),
            ...static::layout(),
        ];
    }

    /**
     * Shared "Responsive layout" controls — three explicit breakpoint
     * sections (mobile / tablet / desktop) so the editor can switch between
     * flex and grid and tune columns, direction, alignment and gap
     * independently at each breakpoint. Stored under `layout.{mobile,tablet,
     * desktop}` and rendered as collapsed Sections so they stay out of the
     * way until needed.
     *
     * @return array<int, Component>
     */
    public static function layout(): array
    {
        $breakpoints = [
            'mobile' => 'Mobile (base)',
            'desktop' => 'Desktop (1024px+)',
        ];

        $make = function (string $key, string $label): Section {
            $path = 'layout.'.$key.'.';

            return Section::make($label)
                ->columns(2)
                ->collapsed()
                ->schema([
                    Select::make($path.'display')
                        ->label('Display')
                        ->options(['grid' => 'Grid', 'flex' => 'Flex'])
                        ->default('grid')
                        ->live(),
                    Select::make($path.'gap')
                        ->label('Gap')
                        ->options([
                            '2' => '8px', '4' => '16px', '6' => '24px',
                            '8' => '32px', '10' => '40px', '12' => '48px',
                        ])
                        ->default('6'),
                    Select::make($path.'columns')
                        ->label('Columns (grid)')
                        ->options([
                            '1' => '1', '2' => '2', '3' => '3',
                            '4' => '4', '5' => '5', '6' => '6',
                        ])
                        ->default('3')
                        ->visible(fn (callable $get): bool => $get($path.'display') === 'grid'),
                    Select::make($path.'direction')
                        ->label('Direction (flex)')
                        ->options(['row' => 'Row', 'col' => 'Column'])
                        ->default('row')
                        ->visible(fn (callable $get): bool => $get($path.'display') === 'flex'),
                    Select::make($path.'wrap')
                        ->label('Wrap (flex)')
                        ->options(['wrap' => 'Wrap', 'nowrap' => 'No wrap'])
                        ->default('wrap')
                        ->visible(fn (callable $get): bool => $get($path.'display') === 'flex'),
                    Select::make($path.'justify')
                        ->label('Justify')
                        ->options([
                            'start' => 'Start', 'center' => 'Center',
                            'end' => 'End', 'between' => 'Space between',
                        ])
                        ->default('start'),
                    Select::make($path.'items')
                        ->label('Align items')
                        ->options([
                            'start' => 'Start', 'center' => 'Center', 'end' => 'End',
                        ])
                        ->default('start'),
                ]);
        };

        return [
            Section::make('Responsive layout')
                ->description('Control flex vs grid and columns/alignment per breakpoint. Mobile applies to all sizes unless overridden by tablet/desktop.')
                ->collapsed()
                ->schema([
                    $make('mobile', 'Mobile (base)'),
                    $make('tablet', 'Tablet (632px+)'),
                    $make('desktop', 'Desktop (1024px+)'),
                ]),
        ];
    }

    /**
     * Build the Tailwind class string for an item container from a block's
     * `layout` config. Keyed by breakpoint: `mobile` (base), `desktop`
     * (`lg:`).
     *
     * @param  array<string, array<string, mixed>>|null  $layout
     */
    public static function layoutClasses(?array $layout): string
    {
        if (empty($layout)) {
            return '';
        }

        $map = [
            'mobile' => '',
            'tablet' => 'tab:',
            'desktop' => 'lg:',
        ];

        $classes = [];

        foreach ($map as $bp => $p) {
            $row = $layout[$bp] ?? null;
            if (! is_array($row)) {
                continue;
            }
            $display = $row['display'] ?? 'grid';
            $gap = $row['gap'] ?? '6';

            if ($display === 'flex') {
                $classes[] = $p.'flex';
                $classes[] = $p.($row['direction'] === 'col' ? 'flex-col' : 'flex-row');
                $classes[] = $p.($row['wrap'] === 'nowrap' ? 'flex-nowrap' : 'flex-wrap');
            } else {
                $classes[] = $p.'grid';
                $cols = max(1, min(6, (int) ($row['columns'] ?? 3)));
                $classes[] = $p.'grid-cols-'.$cols;
            }

            $classes[] = $p.'gap-'.$gap;

            $justifyClass = match ($row['justify'] ?? 'start') {
                'center' => 'justify-center',
                'end' => 'justify-end',
                'between' => 'justify-between',
                default => 'justify-start',
            };
            $classes[] = $p.$justifyClass;

            $itemsClass = match ($row['items'] ?? 'start') {
                'center' => 'items-center',
                'end' => 'items-end',
                default => 'items-start',
            };
            $classes[] = $p.$itemsClass;
        }

        return implode(' ', $classes);
    }
}
