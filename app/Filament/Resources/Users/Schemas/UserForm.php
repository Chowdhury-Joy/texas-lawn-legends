<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\AccessPermissions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Username / Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('role')
                    ->options(collect(UserRole::cases())->mapWithKeys(
                        fn (UserRole $role) => [$role->value => $role->getLabel()]
                    ))
                    ->default(UserRole::Content->value)
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->dehydrateStateUsing(fn (?string $state) => bcrypt($state))
                    ->maxLength(255),
                static::accessSection(),
            ]);
    }

    /**
     * Per-user access matrix. Only meaningful for non-admins (admins are
     * always superusers), so it is hidden from admin records and shown as
     * a grouped checkbox list sourced from the central permission registry.
     */
    protected static function accessSection(): Section
    {
        return Section::make('Access')
            ->description('Tick the areas this user may open. Start from the role preset, then grant or revoke individually. Admins always have full access.')
            ->visible(fn (?User $record): bool => ! $record?->isAdmin())
            ->schema([
                CheckboxList::make('access_keys')
                    ->label('Granted areas')
                    ->options(AccessPermissions::groupedOptions())
                    ->columns(2)
                    ->gridDirection('row')
                    ->default(fn (?User $record) => $record?->effectiveKeys() ?? [])
                    ->dehydrated()
                    ->afterStateUpdated(fn () => null),
            ]);
    }
}
