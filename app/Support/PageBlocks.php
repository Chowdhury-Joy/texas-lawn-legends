<?php

namespace App\Support;

use Filament\Forms\Components\Builder\Block;
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
use Filament\Schemas\Components\Utilities\Get;
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
     * Single source of truth for available site themes.
     *
     * @return array<string, string>
     */
    public static function themes(): array
    {
        return [
            'clean' => 'Clean (flat, no shadows — default)',
            'minimal' => 'Minimal (flat with soft modern shadows)',
            'editorial' => 'Editorial (serif display, airy whitespace)',
            'rounded' => 'Rounded (pill buttons, large radii)',
            'retro' => 'Retro (70s earthy throwback)',
            'bold' => 'Bold (neo-brutalist hard shadows & borders)',
        ];
    }

    /**
     * Preset block section stacks for one-click starter layouts.
     *
     * @return array<string, array{label: string, description: string, blocks: array<int, array{type: string, data: array<string, mixed>}>}>
     */
    public static function presets(): array
    {
        return [
            'landing_page' => [
                'label' => 'High-Converting Landing Page',
                'description' => 'Hero, Trust Bar, 3-Step Process, Service Matrix, Testimonials, and CTA Banner.',
                'blocks' => [
                    ['type' => 'hero', 'data' => ['heading' => 'Transform Your Dallas Yard Into An Outdoor Retreat.', 'subheading' => 'Professional design, precision hardscaping, and premier maintenance you can rely on.']],
                    ['type' => 'trust_bar', 'data' => []],
                    ['type' => 'three_step', 'data' => ['heading' => 'Our 3-Step Process', 'steps' => [
                        ['number' => '01', 'title' => 'Digital Consultation', 'body' => 'Instant valuation and property assessment.'],
                        ['number' => '02', 'title' => 'Custom 3D Design', 'body' => 'Tailored landscape plans for your home.'],
                        ['number' => '03', 'title' => 'Precision Build', 'body' => 'Professional execution on time.'],
                    ]]],
                    ['type' => 'service_matrix', 'data' => ['create_suite_heading' => 'The Create Suite', 'care_suite_heading' => 'The Care Suite']],
                    ['type' => 'testimonial_grid', 'data' => ['heading' => 'Verified Neighborhood Reviews', 'featured_only' => true]],
                    ['type' => 'cta_banner', 'data' => ['heading' => 'Ready to Start Your Project?', 'subheading' => 'Get a verified local price range in under 2 minutes.', 'button_label' => 'Start Your Free Estimate']],
                ],
            ],
            'portfolio_showcase' => [
                'label' => 'Portfolio & Proof Showcase',
                'description' => 'Hero, Gallery, Neighborhood Proof, Review Spotlight, and CTA Banner.',
                'blocks' => [
                    ['type' => 'hero', 'data' => ['heading' => 'Explore Our Premier Dallas Landscapes.', 'subheading' => 'Real transformations from Kessler Park, Highland Park, and Bishop Arts.']],
                    ['type' => 'gallery', 'data' => ['images' => [
                        ['image' => null, 'caption' => 'Flagstone Patio & Fire Pit'],
                        ['image' => null, 'caption' => 'Retaining Wall & Sod Install'],
                        ['image' => null, 'caption' => 'Outdoor Living Build'],
                    ]]],
                    ['type' => 'neighborhood_proof', 'data' => ['heading' => 'Verified Local Proof', 'subheading' => 'Real Dallas homeowners share their experience.']],
                    ['type' => 'review_spotlight', 'data' => ['heading' => 'Spotlight Review', 'eyebrow' => 'Customer Story']],
                    ['type' => 'cta_banner', 'data' => ['heading' => 'Transform Your Outdoor Living Space', 'button_label' => 'Get Your Instant Price']],
                ],
            ],
            'services_suite' => [
                'label' => 'Services & Care Overview',
                'description' => 'Hero, Icon Features, Service Matrix, FAQ, and CTA Banner.',
                'blocks' => [
                    ['type' => 'hero', 'data' => ['heading' => 'Comprehensive Landscaping & Lawn Care Services.', 'subheading' => 'Full-service design, build, and seasonal maintenance packages.']],
                    ['type' => 'icon_feature', 'data' => ['heading' => 'Why Dallas Homeowners Choose Us', 'features' => [
                        ['icon' => 'sparkles', 'title' => 'Licensed & Insured', 'body' => 'Full protection for your property.'],
                        ['icon' => 'sparkles', 'title' => 'Transparent Pricing', 'body' => 'No hidden fees or surprise costs.'],
                        ['icon' => 'sparkles', 'title' => 'Dedicated Project Manager', 'body' => 'One point of contact from start to finish.'],
                    ]]],
                    ['type' => 'service_matrix', 'data' => []],
                    ['type' => 'faq', 'data' => ['heading' => 'Frequently Asked Questions', 'items' => [
                        ['question' => 'How long does a typical estimate take?', 'answer' => 'Our digital calculator provides an instant price range in 2 minutes.'],
                        ['question' => 'Do you handle permits and HOA approvals?', 'answer' => 'Yes, we manage all required city permits and HOA documentation.'],
                    ]]],
                    ['type' => 'cta_banner', 'data' => ['heading' => 'Schedule Your Site Visit Today', 'button_label' => 'Book Free Consultation']],
                ],
            ],
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
     * Builder blocks for every registered type, built from labels() so the
     * homepage editor and the Pages resource can never drift apart.
     *
     * @return array<int, Block>
     */
    public static function builderBlocks(): array
    {
        return array_map(
            fn (string $type, string $label): Block => Block::make($type)
                ->label(function (?array $state) use ($label): string {
                    if (empty($state)) {
                        return $label;
                    }

                    // First check flat top-level fields
                    $title = $state['heading']
                        ?? $state['title']
                        ?? $state['eyebrow']
                        ?? $state['quote']
                        ?? $state['create_suite_heading']
                        ?? null;

                    // Then scan inside text_elements repeater for the first heading or eyebrow
                    if (! filled($title) && ! empty($state['text_elements'])) {
                        foreach ($state['text_elements'] as $el) {
                            if (in_array($el['type'] ?? '', ['heading', 'eyebrow'], true) && filled($el['text'] ?? null)) {
                                $title = $el['text'];
                                break;
                            }
                        }
                    }

                    return filled($title) ? "{$label} — {$title}" : $label;
                })
                ->schema(static::fields($type)),
            static::all(),
            array_values(static::labels()),
        );
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

    /**
     * Auto-migrate a block's classic flat fields into the `text_elements` repeater so that
     * existing database content is immediately visible and editable in the admin form.
     *
     * Call this in any form lifecycle hook that fires *before* the form is filled
     * (e.g. `mount()` for settings pages, `mutateFormDataBeforeFill()` for resource edit pages).
     *
     * Rules:
     * - If `text_elements` is already non-empty, return $data unchanged — the editor has already
     *   taken ownership of the repeater and we must not overwrite their order.
     * - Otherwise, build a seed list from whichever classic fields are present and non-empty,
     *   in the original default render order, and set them as `text_elements`.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function seedTextElementsForBlock(string $type, array $data): array
    {
        // Already managed by the repeater — hands off.
        if (! empty($data['text_elements'])) {
            return $data;
        }

        $seeds = match ($type) {
            'hero' => array_filter([
                filled($data['eyebrow'] ?? null) ? ['type' => 'eyebrow',       'text' => $data['eyebrow']] : null,
                (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                                                           ? ['type' => 'heading',        'text' => $data['heading'] ?? null] : null,
                filled($data['subheading'] ?? null) ? ['type' => 'subheading',     'text' => $data['subheading']] : null,
                (! array_key_exists('cta_primary_label', $data) || filled($data['cta_primary_label'] ?? null))
                                                           ? ['type' => 'primary_cta',    'text' => $data['cta_primary_label'] ?? null] : null,
                (! array_key_exists('cta_secondary_label', $data) || filled($data['cta_secondary_label'] ?? null))
                                                           ? ['type' => 'secondary_cta',  'text' => $data['cta_secondary_label'] ?? null] : null,
            ]),
            'cta_banner' => array_filter([
                filled($data['eyebrow'] ?? null) ? ['type' => 'eyebrow',  'text' => $data['eyebrow']] : null,
                (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                                                           ? ['type' => 'heading',  'text' => $data['heading'] ?? null] : null,
                filled($data['subheading'] ?? null) ? ['type' => 'subheading', 'text' => $data['subheading']] : null,
                filled($data['button_label'] ?? null) ? ['type' => 'button',   'text' => $data['button_label']] : null,
            ]),
            'three_step' => array_filter([
                filled($data['eyebrow'] ?? null) ? ['type' => 'eyebrow',    'text' => $data['eyebrow']] : null,
                (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                                                           ? ['type' => 'heading',    'text' => $data['heading'] ?? null] : null,
                filled($data['subheading'] ?? null) ? ['type' => 'subheading', 'text' => $data['subheading']] : null,
            ]),
            default => [],
        };

        $seeds = array_values($seeds); // re-index after array_filter

        if (! empty($seeds)) {
            $data['text_elements'] = $seeds;
        }

        return $data;
    }

    /**
     * Apply seedTextElementsForBlock() across an entire blocks array (for use in
     * form lifecycle hooks that operate on the full blocks collection).
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    public static function seedTextElementsForBlocks(array $blocks): array
    {
        return array_map(
            fn (array $block): array => array_merge($block, [
                'data' => static::seedTextElementsForBlock($block['type'] ?? '', $block['data'] ?? []),
            ]),
            $blocks,
        );
    }

    private static function hero(): array
    {
        return [
            static::textElementsRepeater(['eyebrow', 'heading', 'subheading', 'primary_cta', 'secondary_cta']),
            FileUpload::make('media_image')
                ->label('Hero image (optional)')
                ->image()->directory('homepage')->disk('public')->visibility('public')
                ->imageEditor()->imageEditorAspectRatios([16 / 9, 21 / 9])
                ->helperText('Recommended 16:9 ratio. Falls back to default desktop image when empty.')
                ->columnSpanFull(),
            FileUpload::make('media_image_mobile')
                ->label('Hero mobile image (optional)')
                ->image()->directory('homepage')->disk('public')->visibility('public')
                ->imageEditor()->imageEditorAspectRatios([1, 4 / 5])
                ->helperText('Used on small screens. Falls back to default mobile image when empty.')
                ->columnSpanFull(),
            TextInput::make('media_badge')->label('Media badge')->placeholder('e.g. Featured Project'),
            TextInput::make('media_neighborhood')->label('Media neighborhood label')->placeholder('e.g. Highland Park, TX'),
            TextInput::make('media_title')->label('Media project title')->placeholder('e.g. Custom Outdoor Living & Fire Pit'),
            TextInput::make('media_subtitle')->label('Media project subtitle')->placeholder('e.g. Completed June 2026'),
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
            static::textElementsRepeater(['eyebrow', 'heading', 'subheading']),
            Repeater::make('steps')
                ->label('Steps')
                ->minItems(1)
                ->schema([
                    TextInput::make('number')->label('Number')->required()->maxLength(4),
                    TextInput::make('title')->label('Title')->required(),
                    Textarea::make('body')->label('Body')->rows(3)->required(),
                ])
                ->columns(1)
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable()
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
            static::textElementsRepeater(['eyebrow', 'heading', 'subheading', 'button']),
            TextInput::make('button_url')->label('Button URL')->helperText('If a Button subsection uses a custom URL, set it here. Defaults to /estimate.'),
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
            TextInput::make('eyebrow')->label('Eyebrow tag'),
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
                ->minItems(1)
                ->schema([
                    FileUpload::make('image')->label('Image')->image()->directory('pages')->disk('public')->visibility('public')->imageEditor()->imageEditorAspectRatios([4 / 3, 1])->required(),
                    TextInput::make('caption')->label('Caption'),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable()
                ->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Untitled image'),
            ...static::layout(),
        ];
    }

    private static function faq(): array
    {
        return [
            TextInput::make('heading')->label('Heading'),
            Repeater::make('items')
                ->label('Questions')
                ->minItems(1)
                ->schema([
                    TextInput::make('question')->label('Question')->required(),
                    Textarea::make('answer')->label('Answer')->rows(3)->required(),
                ])
                ->columns(1)
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable()
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
                ->minItems(1)
                ->schema([
                    TextInput::make('value')->label('Value (e.g. 250+)')->required(),
                    TextInput::make('label')->label('Label')->required(),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable()
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
                ->minItems(1)
                ->schema([
                    TextInput::make('value')->label('Value (e.g. 250+)')->required(),
                    TextInput::make('label')->label('Label')->required(),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable()
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
                ->minItems(1)
                ->schema([
                    TextInput::make('icon')->label('Icon name (svg-icon)'),
                    TextInput::make('title')->label('Title')->required(),
                    Textarea::make('body')->label('Body')->rows(2),
                ])
                ->columns(1)
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable()
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
                ->minItems(1)
                ->schema([
                    FileUpload::make('image')->label('Logo')->image()->directory('pages')->disk('public')->visibility('public')->imageEditor()->imageEditorAspectRatios([3 / 2, 2 / 1])->required(),
                    TextInput::make('label')->label('Alt text / label'),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable()
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Untitled logo'),
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
     * Draggable text & button subsections repeater for customizing element order.
     *
     * @param  array<int, string>  $allowedTypes
     */
    public static function textElementsRepeater(array $allowedTypes = ['eyebrow', 'heading', 'subheading']): Repeater
    {
        $options = [
            'eyebrow' => 'Eyebrow Tag (Badge)',
            'heading' => 'Headline / Main Title',
            'subheading' => 'Sub-heading / Paragraph',
            'primary_cta' => 'Primary Button (Instant Price Form)',
            'secondary_cta' => 'Secondary Button (Phone Call / Text)',
            'button' => 'Action Button (Link)',
        ];

        $filteredOptions = array_intersect_key($options, array_flip($allowedTypes));

        return Repeater::make('text_elements')
            ->label('Subsections Layout (Drag to Reorder)')
            ->helperText('Drag items to reorder how Eyebrow, Headline, Subheading, and Buttons appear in this block.')
            ->schema([
                Select::make('type')
                    ->label('Subsection Type')
                    ->options($filteredOptions)
                    ->required()
                    ->live(),
                TextInput::make('text')
                    ->label('Text / Label Content')
                    ->placeholder('Enter text content...')
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->label('Button URL (optional)')
                    ->placeholder('e.g. /estimate')
                    ->visible(fn (Get $get) => $get('type') === 'button'),
            ])
            ->columns(2)
            ->reorderable()
            ->cloneable()
            ->collapsible()
            ->collapsed()
            ->columnSpanFull()
            ->itemLabel(fn (array $state): string => match ($state['type'] ?? '') {
                'eyebrow' => '🏷️ Eyebrow — '.($state['text'] ?? 'Badge'),
                'heading' => '🔤 Headline — '.($state['text'] ?? 'Title'),
                'subheading' => '📝 Subheading — '.($state['text'] ?? 'Description'),
                'primary_cta' => '🔘 Primary Button — '.($state['text'] ?? 'Price Form'),
                'secondary_cta' => '📞 Secondary Button — '.($state['text'] ?? 'Call/Text'),
                'button' => '🔗 Button — '.($state['text'] ?? 'Action Link'),
                default => 'Subsection',
            });
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
                            'auto' => 'Auto (fit to content — recommended)',
                            '1' => '1', '2' => '2', '3' => '3',
                            '4' => '4', '5' => '5', '6' => '6',
                        ])
                        ->default('auto')
                        ->helperText('Auto sizes columns to the available width and item count, so it never leaves an orphaned item on its own row. Pick a fixed number only to force an exact count.')
                        ->visible(fn (Get $get): bool => $get($path.'display') === 'grid'),
                    Select::make($path.'direction')
                        ->label('Direction (flex)')
                        ->options(['row' => 'Row', 'col' => 'Column'])
                        ->default('row')
                        ->visible(fn (Get $get): bool => $get($path.'display') === 'flex'),
                    Select::make($path.'wrap')
                        ->label('Wrap (flex)')
                        ->options(['wrap' => 'Wrap', 'nowrap' => 'No wrap'])
                        ->default('wrap')
                        ->visible(fn (Get $get): bool => $get($path.'display') === 'flex'),
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
                ->description('Control flex vs grid and columns/alignment per breakpoint. Visible to users with designer/advanced layout permission.')
                ->collapsed()
                ->visible(fn (): bool => auth()->user()?->canAccessKey('settings.advanced_layout') ?? false)
                ->schema([
                    $make('mobile', 'Mobile (base)'),
                    $make('tablet', 'Tablet (632px+)'),
                    $make('desktop', 'Desktop (1024px+)'),
                ]),
        ];
    }

    /**
     * Build the Tailwind class string for an item container from a block's
     * `layout` config. Keyed by breakpoint: `mobile` (base), `tablet`
     * (`tab:`), `desktop` (`lg:`).
     *
     * `columns` defaults to `auto`, which emits a `repeat(auto-fit,
     * minmax(200px,1fr))` grid instead of a fixed `grid-cols-N` — column
     * count then follows available width and item count on its own, so a
     * gallery of 3 images and one of 7 both lay out cleanly without anyone
     * having picked a number. A numeric `columns` value still forces an
     * exact, fixed count for whoever wants to override it.
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
                $columns = $row['columns'] ?? 'auto';
                $classes[] = $p.(is_numeric($columns)
                    ? 'grid-cols-'.max(1, min(6, (int) $columns))
                    : 'grid-cols-[repeat(auto-fit,minmax(200px,1fr))]');
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
