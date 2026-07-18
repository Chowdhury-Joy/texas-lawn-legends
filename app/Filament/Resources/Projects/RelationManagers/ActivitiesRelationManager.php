<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Concerns\ActivityLogRelationManager;
use Filament\Resources\RelationManagers\RelationManager;

class ActivitiesRelationManager extends RelationManager
{
    use ActivityLogRelationManager;

    protected static string $relationship = 'activitiesAsSubject';

    protected static ?string $title = 'Activity log';
}
