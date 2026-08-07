<?php

namespace App\Filament\Resources\Equipment\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Equipment\EquipmentResource;
use Filament\Resources\Pages\EditRecord;

class EditEquipment extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = EquipmentResource::class;
}
