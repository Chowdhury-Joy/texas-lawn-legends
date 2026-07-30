<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Services\ServiceResource;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = ServiceResource::class;
}
