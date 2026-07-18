<?php

namespace App\Filament\Resources\ProgressPhotos\Pages;

use App\Filament\Resources\ProgressPhotos\ProgressPhotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgressPhotos extends ListRecords
{
    protected static string $resource = ProgressPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
