<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\Milestones\MilestoneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMilestones extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = MilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
