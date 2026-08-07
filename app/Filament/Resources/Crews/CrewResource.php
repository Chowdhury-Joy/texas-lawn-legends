<?php

namespace App\Filament\Resources\Crews;

use App\Filament\Concerns\RoleRestricted;
use App\Filament\Resources\Crews\Pages\CreateCrew;
use App\Filament\Resources\Crews\Pages\EditCrew;
use App\Filament\Resources\Crews\Pages\ListCrews;
use App\Filament\Resources\Crews\Schemas\CrewForm;
use App\Filament\Resources\Crews\Tables\CrewsTable;
use App\Models\Crew;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CrewResource extends Resource
{
    use RoleRestricted;

    protected static ?string $model = Crew::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CrewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrewsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrews::route('/'),
            'create' => CreateCrew::route('/create'),
            'edit' => EditCrew::route('/{record}/edit'),
        ];
    }
}
