<?php

namespace App\Filament\Resources\TimeEntries\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\TimeEntries\TimeEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTimeEntries extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = TimeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
