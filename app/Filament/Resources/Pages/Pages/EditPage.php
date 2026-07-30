<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Pages\PageResource;
use App\Support\PageBlocks;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = PageResource::class;

    protected string $view = 'filament.resources.pages.pages.edit';

    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    /**
     * Auto-seed text_elements from classic flat fields before the form fills,
     * so existing blocks are immediately visible and editable in the admin.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['blocks'])) {
            $data['blocks'] = PageBlocks::seedTextElementsForBlocks($data['blocks']);
        }

        return $data;
    }
}
