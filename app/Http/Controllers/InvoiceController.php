<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice)
    {
        abort_if($invoice->status === InvoiceStatus::Draft, 404);

        $invoice->load(['items', 'project', 'lead']);

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }
}
