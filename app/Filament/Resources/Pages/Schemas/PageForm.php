<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
