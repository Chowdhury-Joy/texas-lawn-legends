<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageOperations extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Operations Alerts';

    protected static ?string $title = 'Operations Alerts';

    protected function settingsGroup(): string
    {
        return 'operations';
    }

    protected function settingsMap(): array
    {
        return [
            'operations_webhook_url' => 'string',
            'operations_alert_email' => 'string',
            'lead_escalation_minutes' => 'integer',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Dispatcher Alerts')
                ->description('Where high-priority operational alerts (add-on orders, stalled leads) are sent.')
                ->schema([
                    TextInput::make('operations_webhook_url')
                        ->label('Operations webhook URL')
                        ->url()
                        ->placeholder('https://hooks.example.com/operations')
                        ->helperText('Outbound POST target for real-time alerts. Leave blank to log only.'),
                    TextInput::make('operations_alert_email')
                        ->label('Operations email')
                        ->email(),
                ]),
            Section::make('Lead Escalation')
                ->schema([
                    TextInput::make('lead_escalation_minutes')
                        ->label('Escalate after (minutes)')
                        ->numeric()
                        ->suffix('minutes')
                        ->helperText('If a lead stalls on the estimate screen this long without booking, trigger a re-engagement alert.'),
                ]),
        ];
    }
}
