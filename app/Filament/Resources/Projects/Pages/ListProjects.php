<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
