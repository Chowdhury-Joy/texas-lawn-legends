<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Livewire\Admin\PageBuilder;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Livewire as LivewireSchemaComponent;
use Filament\Schemas\Schema;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
            LivewireSchemaComponent::make(PageBuilder::class, ['page' => $this->getRecord()])
                ->key('page-builder-'.$this->getRecord()->getKey()),
        ]);
    }
}
