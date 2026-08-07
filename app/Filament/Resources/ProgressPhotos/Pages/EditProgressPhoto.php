<?php

namespace App\Filament\Resources\ProgressPhotos\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\ProgressPhotos\ProgressPhotoResource;
use Filament\Resources\Pages\EditRecord;

class EditProgressPhoto extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = ProgressPhotoResource::class;
}
