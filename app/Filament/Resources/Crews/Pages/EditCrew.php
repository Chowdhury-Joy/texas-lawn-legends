<?php

namespace App\Filament\Resources\Crews\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Crews\CrewResource;
use Filament\Resources\Pages\EditRecord;

class EditCrew extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = CrewResource::class;
}
