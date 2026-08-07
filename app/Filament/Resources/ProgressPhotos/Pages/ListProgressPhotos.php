<?php

namespace App\Filament\Resources\ProgressPhotos\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\ProgressPhotos\ProgressPhotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgressPhotos extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = ProgressPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
