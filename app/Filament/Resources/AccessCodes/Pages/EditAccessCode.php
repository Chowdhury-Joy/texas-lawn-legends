<?php

namespace App\Filament\Resources\AccessCodes\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\AccessCodes\AccessCodeResource;
use Filament\Resources\Pages\EditRecord;

class EditAccessCode extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = AccessCodeResource::class;
}
