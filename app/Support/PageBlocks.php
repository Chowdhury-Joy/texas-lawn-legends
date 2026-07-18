<?php

namespace App\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
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
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function forHomepage(): array
    {
        return ['hero', 'trust_bar', 'three_step', 'service_matrix', 'neighborhood_proof', 'cta_banner'];
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
                ->helperText('Falls back to the styled placeholder card when empty.')
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
        ];
    }

    private static function serviceMatrix(): array
    {
        return [
            TextInput::make('create_suite_heading')->label('Create Suite heading'),
            TextInput::make('care_suite_heading')->label('Care Suite heading'),
        ];
    }

    private static function neighborhoodProof(): array
    {
        return [
            TextInput::make('heading')->label('Heading'),
            TextInput::make('subheading')->label('Sub-heading'),
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
            FileUpload::make('image')->label('Image')->image()->directory('pages')->disk('public')->visibility('public')->required(),
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
                    FileUpload::make('image')->label('Image')->image()->directory('pages')->disk('public')->visibility('public')->required(),
                    TextInput::make('caption')->label('Caption'),
                ])
                ->columns(2)
                ->reorderable()
                ->collapsible(),
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
}
