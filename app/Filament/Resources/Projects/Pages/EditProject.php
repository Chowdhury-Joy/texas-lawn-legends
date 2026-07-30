<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = ProjectResource::class;
}
