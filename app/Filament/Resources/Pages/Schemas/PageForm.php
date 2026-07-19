<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Support\PageBlocks;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->notIn(['estimate', 'portal', 'dashboard', 'admin', 'robots.txt', 'sitemap.xml'])
                            ->helperText('URL path, e.g. "about-us" renders at /about-us.'),
                        Toggle::make('is_published')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
                Section::make('Page Content')
                    ->schema([
                        Builder::make('blocks')
                            ->label('Page Sections')
                            ->blocks([
                                Builder\Block::make('hero')
                                    ->label('Hero')
                                    ->schema(PageBlocks::fields('hero')),
                                Builder\Block::make('trust_bar')
                                    ->label('Trust Bar')
                                    ->schema(PageBlocks::fields('trust_bar')),
                                Builder\Block::make('three_step')
                                    ->label('3-Step Process')
                                    ->schema(PageBlocks::fields('three_step')),
                                Builder\Block::make('service_matrix')
                                    ->label('Service Matrix')
                                    ->schema(PageBlocks::fields('service_matrix')),
                                Builder\Block::make('neighborhood_proof')
                                    ->label('Neighborhood Proof')
                                    ->schema(PageBlocks::fields('neighborhood_proof')),
                                Builder\Block::make('cta_banner')
                                    ->label('CTA Banner')
                                    ->schema(PageBlocks::fields('cta_banner')),
                                Builder\Block::make('rich_text')
                                    ->label('Rich Text')
                                    ->schema(PageBlocks::fields('rich_text')),
                                Builder\Block::make('image_text_split')
                                    ->label('Image + Text Split')
                                    ->schema(PageBlocks::fields('image_text_split')),
                                Builder\Block::make('gallery')
                                    ->label('Gallery')
                                    ->schema(PageBlocks::fields('gallery')),
                                Builder\Block::make('faq')
                                    ->label('FAQ')
                                    ->schema(PageBlocks::fields('faq')),
                                Builder\Block::make('about')
                                    ->label('About Us')
                                    ->schema(PageBlocks::fields('about')),
                                Builder\Block::make('stat_band')
                                    ->label('Stat Band')
                                    ->schema(PageBlocks::fields('stat_band')),
                                Builder\Block::make('testimonial_quote')
                                    ->label('Testimonial Quote')
                                    ->schema(PageBlocks::fields('testimonial_quote')),
                                Builder\Block::make('icon_feature')
                                    ->label('Icon Feature Grid')
                                    ->schema(PageBlocks::fields('icon_feature')),
                                Builder\Block::make('logo_cloud')
                                    ->label('Logo Cloud')
                                    ->schema(PageBlocks::fields('logo_cloud')),
                                Builder\Block::make('testimonial_grid')
                                    ->label('Testimonial Grid')
                                    ->schema(PageBlocks::fields('testimonial_grid')),
                                Builder\Block::make('review_spotlight')
                                    ->label('Review Spotlight')
                                    ->schema(PageBlocks::fields('review_spotlight')),
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make('SEO')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('seo_title')->label('Meta title')->maxLength(255),
                        TextInput::make('seo_description')->label('Meta description')->maxLength(255),
                        FileUpload::make('seo_image')
                            ->label('Social share image')
                            ->image()->directory('pages')->disk('public')->visibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
