<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
