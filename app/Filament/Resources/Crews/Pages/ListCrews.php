<?php

namespace App\Filament\Resources\Crews\Pages;

use App\Filament\Resources\Crews\CrewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrews extends ListRecords
{
    protected static string $resource = CrewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
