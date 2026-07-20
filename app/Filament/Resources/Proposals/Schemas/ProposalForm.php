<?php

namespace App\Filament\Resources\Proposals\Schemas;

use App\Enums\ProposalStatus;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProposalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Proposal Details')
                    ->columns(2)
                    ->schema([
                        Select::make('lead_id')
                            ->relationship('lead', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Linked Lead'),
                        Select::make('project_id')
                            ->relationship('project', 'project_title')
                            ->searchable()
                            ->preload()
                            ->label('Linked Project (After Conversion)'),
                        TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),
                        Select::make('status')
                            ->options(ProposalStatus::class)
                            ->required()
                            ->native(false)
                            ->default(ProposalStatus::Draft->value),
                        DatePicker::make('expires_at'),
                    ]),
                Section::make('Proposal Content')
                    ->schema([
                        Builder::make('content')
                            ->blocks([
                                Builder\Block::make('text_block')
                                    ->schema([
                                        RichEditor::make('content')->required(),
                                    ]),
                                Builder\Block::make('pricing_table')
                                    ->schema([
                                        TextInput::make('title')->default('Itemized Costs'),
                                        Builder::make('items')
                                            ->blocks([
                                                Builder\Block::make('line_item')
                                                    ->schema([
                                                        TextInput::make('description')->required(),
                                                        TextInput::make('amount')->numeric()->prefix('$')->required(),
                                                    ]),
                                            ]),
                                    ]),
                                Builder\Block::make('image_showcase')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->multiple()
                                            ->image()
                                            ->directory('proposals'),
                                        TextInput::make('caption'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
