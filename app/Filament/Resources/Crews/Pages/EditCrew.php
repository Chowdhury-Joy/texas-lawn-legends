<?php

namespace App\Filament\Resources\Crews\Pages;

use App\Filament\Resources\Crews\CrewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrew extends EditRecord
{
    protected static string $resource = CrewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
