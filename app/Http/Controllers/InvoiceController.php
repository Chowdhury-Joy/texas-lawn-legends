<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'project', 'lead']);

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }
}
