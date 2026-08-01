<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
