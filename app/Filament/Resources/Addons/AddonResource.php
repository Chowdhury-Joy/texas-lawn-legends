<?php

namespace App\Filament\Resources\Addons;

use App\Enums\UserRole;
use App\Filament\Concerns\RoleRestricted;
use App\Filament\Resources\Addons\Pages\CreateAddon;
use App\Filament\Resources\Addons\Pages\EditAddon;
use App\Filament\Resources\Addons\Pages\ListAddons;
use App\Filament\Resources\Addons\Schemas\AddonForm;
use App\Filament\Resources\Addons\Tables\AddonsTable;
use App\Models\Addon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AddonResource extends Resource
{
    use RoleRestricted;

    public static function allowedRoles(): array
    {
        return [UserRole::Admin, UserRole::Content, UserRole::Operations];
    }

    protected static ?string $model = Addon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return AddonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AddonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAddons::route('/'),
            'create' => CreateAddon::route('/create'),
            'edit' => EditAddon::route('/{record}/edit'),
        ];
    }
}
