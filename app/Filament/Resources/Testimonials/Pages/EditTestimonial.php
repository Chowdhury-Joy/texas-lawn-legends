<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = TestimonialResource::class;
}
