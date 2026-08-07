<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Milestones\MilestoneResource;
use Filament\Resources\Pages\EditRecord;

class EditMilestone extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = MilestoneResource::class;
}
