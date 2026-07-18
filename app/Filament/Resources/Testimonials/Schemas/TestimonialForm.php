<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('author')
                    ->required()
                    ->maxLength(255),
                TextInput::make('neighborhood')
                    ->required()
                    ->maxLength(255),
                Select::make('rating')
                    ->required()
                    ->native(false)
                    ->default(5)
                    ->options([
                        1 => '★☆☆☆☆ (1)',
                        2 => '★★☆☆☆ (2)',
                        3 => '★★★☆☆ (3)',
                        4 => '★★★★☆ (4)',
                        5 => '★★★★★ (5)',
                    ]),
                TextInput::make('service_tag')
                    ->maxLength(255)
                    ->helperText('e.g. Retaining Walls, Precision Mowing.'),
                Textarea::make('review_text')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->helperText('Featured reviews surface on the homepage proof section.'),
            ]);
    }
}
