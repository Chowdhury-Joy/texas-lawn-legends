<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Filament\Concerns\RoleRestricted;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use App\Support\AccessPermissions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    use RoleRestricted;

    public static function allowedRoles(): array
    {
        return [UserRole::Admin];
    }

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Sync the submitted access_keys (checkbox list) into permission rows.
     * The list only carries checked keys, so we diff against the role's
     * default preset: a checked key that the role already grants needs no
     * override (delete any stray row), while a checked key beyond the
     * preset becomes an explicit grant, and an unchecked key the role
     * would have granted becomes an explicit revoke.
     * Admins are always superusers, so we never persist overrides for them.
     */
    protected function afterSave(): void
    {
        /** @var User $record */
        $record = $this->getRecord();

        if ($record->isAdmin()) {
            $record->permissions()->delete();

            return;
        }

        $checked = collect((array) ($this->data['access_keys'] ?? []));
        $defaults = AccessPermissions::defaultsFor($record->role);
        $allKeys = array_keys(AccessPermissions::all());

        $record->permissions()->delete();

        foreach ($allKeys as $key) {
            $isChecked = $checked->contains($key);

            if ($isChecked) {
                // Beyond the preset -> explicit grant; matches preset -> no row needed.
                if (! in_array($key, $defaults, true)) {
                    $record->permissions()->create(['key' => $key, 'allowed' => true]);
                }
            } else {
                // Unchecked but the role would grant it -> explicit revoke.
                if (in_array($key, $defaults, true)) {
                    $record->permissions()->create(['key' => $key, 'allowed' => false]);
                }
            }
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
