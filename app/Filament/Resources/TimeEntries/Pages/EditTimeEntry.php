<?php

namespace App\Filament\Resources\TimeEntries\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\TimeEntries\TimeEntryResource;
use Filament\Resources\Pages\EditRecord;

class EditTimeEntry extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = TimeEntryResource::class;
}
