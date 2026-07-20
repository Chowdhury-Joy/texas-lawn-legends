<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:escalate-overdue-invoices')]
#[Description('Automatically transitions Sent invoices to Overdue when their due date passes')]
class EscalateOverdueInvoices extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Invoice::query()
            ->where('status', InvoiceStatus::Sent)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->update(['status' => InvoiceStatus::Overdue]);

        $this->info("Escalated {$count} invoice(s) to Overdue status.");
    }
}
