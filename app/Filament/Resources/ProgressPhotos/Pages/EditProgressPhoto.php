<?php

namespace App\Filament\Resources\ProgressPhotos\Pages;

use App\Filament\Resources\ProgressPhotos\ProgressPhotoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProgressPhoto extends EditRecord
{
    protected static string $resource = ProgressPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
