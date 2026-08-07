<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Settings\SettingResource;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = SettingResource::class;
}
