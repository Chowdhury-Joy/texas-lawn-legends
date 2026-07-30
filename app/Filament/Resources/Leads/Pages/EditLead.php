<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Leads\LeadResource;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = LeadResource::class;
}
