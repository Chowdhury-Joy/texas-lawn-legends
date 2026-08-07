<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTestimonials extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }
}
