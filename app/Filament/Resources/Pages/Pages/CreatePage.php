<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected string $view = 'filament.resources.pages.pages.create';

    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}
