<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManagePricing extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Estimator & Pricing';

    protected static ?string $title = 'Estimator & Pricing';

    protected function settingsGroup(): string
    {
        return 'pricing';
    }

    protected function settingsMap(): array
    {
        return [
            'estimator_mode' => 'string',
            'price_per_sqft_modifier' => 'decimal',
            'estimate_high_multiplier' => 'decimal',
            'estimate_min_sqft' => 'integer',
            'estimate_max_sqft' => 'integer',
            'estimate_custom_threshold' => 'decimal',
            'neighborhood_modifiers' => 'json',
            'complexity_modifiers' => 'json',
            'booking_days_offered' => 'integer',
            'booking_time_slots' => 'json',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Estimator Mode')
                ->description('Controls how customers select services in the public estimate wizard.')
                ->schema([
                    Radio::make('estimator_mode')
                        ->label('Wizard Mode')
                        ->options([
                            'quick' => 'Quick Quote — single service, instant advance (recommended for most businesses)',
                            'full'  => 'Full Estimate — multi-service cart, customer selects multiple services at once',
                        ])
                        ->default('quick')
                        ->required()
                        ->columnSpanFull(),
                ]),
            Section::make('Base Pricing Engine')
                ->description('Low = base rate/sqft × service multiplier × sqft × neighborhood modifier × complexity modifier. High = Low × the high multiplier.')
                ->columns(3)
                ->schema([
                    TextInput::make('price_per_sqft_modifier')
                        ->label('Base rate per sqft ($)')
                        ->numeric()->prefix('$')->step(0.01)->required(),
                    TextInput::make('estimate_high_multiplier')
                        ->label('High-estimate multiplier')
                        ->numeric()->step(0.01)->required(),
                    TextInput::make('estimate_custom_threshold')
                        ->label('Custom-project $ threshold')
                        ->numeric()->prefix('$')->step(1)
                        ->helperText('Estimates above this trigger the "unique project" consultation path.'),
                    TextInput::make('estimate_min_sqft')
                        ->label('Minimum sqft')
                        ->numeric()->suffix('sq ft')->required(),
                    TextInput::make('estimate_max_sqft')
                        ->label('Maximum sqft (slider cap)')
                        ->numeric()->suffix('sq ft')->required(),
                ]),
            Section::make('Neighborhood Modifiers')
                ->description('Per-neighborhood price multipliers applied to the estimate.')
                ->schema([
                    KeyValue::make('neighborhood_modifiers')
                        ->keyLabel('Neighborhood')
                        ->valueLabel('Multiplier')
                        ->addActionLabel('Add neighborhood'),
                ]),
            Section::make('Complexity Modifiers')
                ->description('Multipliers for the project complexity selected in step 3.')
                ->schema([
                    KeyValue::make('complexity_modifiers')
                        ->keyLabel('Complexity')
                        ->valueLabel('Multiplier'),
                ]),
            Section::make('Booking Matrix')
                ->columns(2)
                ->schema([
                    TextInput::make('booking_days_offered')
                        ->label('Business days offered')
                        ->numeric()->required(),
                    TagsInput::make('booking_time_slots')
                        ->label('Time slots')
                        ->placeholder('Add a time slot')
                        ->helperText('Displayed as selectable site-visit times.')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
