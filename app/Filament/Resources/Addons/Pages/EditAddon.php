<?php

namespace App\Filament\Resources\Addons\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Addons\AddonResource;
use Filament\Resources\Pages\EditRecord;

class EditAddon extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = AddonResource::class;
}
