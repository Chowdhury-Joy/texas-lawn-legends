<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use App\Models\Lead;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Information')
                    ->columns(2)
                    ->components([
                        TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->placeholder('Auto-generated (e.g. INV-2026-0001)')
                            ->disabled()
                            ->degraded(),

                        Select::make('status')
                            ->label('Status')
                            ->options(InvoiceStatus::class)
                            ->default(InvoiceStatus::Draft->value)
                            ->required(),

                        Select::make('project_id')
                            ->label('Linked Project')
                            ->relationship('project', 'project_title')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $project = Project::find($state);
                                    if ($project) {
                                        $set('client_name', $project->client_name);
                                    }
                                }
                            }),

                        Select::make('lead_id')
                            ->label('Linked Lead')
                            ->relationship('lead', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $lead = Lead::find($state);
                                    if ($lead) {
                                        $set('client_name', $lead->name);
                                        $set('client_email', $lead->email);
                                    }
                                }
                            }),

                        TextInput::make('client_name')
                            ->label('Client Name')
                            ->required()
                            ->placeholder('e.g. Sarah Jenkins'),

                        TextInput::make('client_email')
                            ->label('Client Email')
                            ->email()
                            ->placeholder('e.g. sarah@example.com'),

                        DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->default(now()->addDays(14))
                            ->required(),
                    ]),

                Section::make('Line Items')
                    ->components([
                        Repeater::make('items')
                            ->relationship()
                            ->columns(12)
                            ->components([
                                TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->placeholder('e.g. Flagstone Patio Installation')
                                    ->columnSpan(6),

                                TextInput::make('quantity')
                                    ->label('Qty / Hours')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('amount', number_format((float) $state * (float) $get('unit_price'), 2, '.', '')))
                                    ->columnSpan(2),

                                TextInput::make('unit_price')
                                    ->label('Unit Price ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('amount', number_format((float) $state * (float) $get('quantity'), 2, '.', '')))
                                    ->columnSpan(2),

                                TextInput::make('amount')
                                    ->label('Amount ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->degraded()
                                    ->columnSpan(2),
                            ])
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),

                Section::make('Totals & Notes')
                    ->columns(2)
                    ->components([
                        Textarea::make('notes')
                            ->label('Invoice Notes / Payment Instructions')
                            ->placeholder('Payment due within 14 days. Make checks payable to Texas Lawn Legends LLC or pay via Zelle/wire.')
                            ->rows(4)
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
