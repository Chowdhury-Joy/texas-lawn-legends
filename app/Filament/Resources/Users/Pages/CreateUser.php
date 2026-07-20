<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Concerns\SyncsAccessPermissions;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use SyncsAccessPermissions;

    protected static string $resource = UserResource::class;
}
