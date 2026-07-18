<?php

namespace App\Filament\Resources\ProgressPhotos;

use App\Filament\Resources\ProgressPhotos\Pages\CreateProgressPhoto;
use App\Filament\Resources\ProgressPhotos\Pages\EditProgressPhoto;
use App\Filament\Resources\ProgressPhotos\Pages\ListProgressPhotos;
use App\Filament\Resources\ProgressPhotos\Schemas\ProgressPhotoForm;
use App\Filament\Resources\ProgressPhotos\Tables\ProgressPhotosTable;
use App\Models\ProgressPhoto;
use BackedEnum;
use Filament\Resources\Resource;
use App\Filament\Concerns\RoleRestricted;
use App\Enums\UserRole;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProgressPhotoResource extends Resource
{
    use RoleRestricted;

    public static function allowedRoles(): array
    {
        return [UserRole::Admin, UserRole::Operations];
    }

    
    protected static ?string $model = ProgressPhoto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'caption';

    public static function form(Schema $schema): Schema
    {
        return ProgressPhotoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgressPhotosTable::configure($table);
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
            'index' => ListProgressPhotos::route('/'),
            'create' => CreateProgressPhoto::route('/create'),
            'edit' => EditProgressPhoto::route('/{record}/edit'),
        ];
    }
}
