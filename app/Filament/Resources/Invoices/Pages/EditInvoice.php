<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = InvoiceResource::class;

    protected function afterSave(): void
    {
        $this->record->calculateTotals();
    }
}
