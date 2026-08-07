<?php

namespace App\Filament\Resources\Addons\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\Addons\AddonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAddons extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = AddonResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
