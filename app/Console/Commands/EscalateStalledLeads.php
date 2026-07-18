<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Services\OperationsNotifier;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('leads:escalate-stalled')]
#[Description('Alert operations about qualified leads that stalled on the estimate screen without booking a site visit')]
class EscalateStalledLeads extends Command
{
    public function handle(OperationsNotifier $notifier): int
    {
        $minutes = max(1, (int) setting('lead_escalation_minutes', 10));

        $stalled = Lead::query()->stalled($minutes)->get();

        foreach ($stalled as $lead) {
            $notifier->dispatch('High-priority lead re-engagement — telephone follow-up needed', [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'neighborhood' => $lead->neighborhood,
                'service_type' => $lead->service_type,
                'estimate_low' => $lead->calculated_estimate_low,
                'estimate_high' => $lead->calculated_estimate_high,
                'stalled_minutes' => $minutes,
                'lead_uuid' => $lead->uuid,
            ]);

            // Mark escalated without touching updated_at / firing model events again.
            $lead->forceFill(['escalated_at' => now()])->saveQuietly();
        }

        $this->info("Escalated {$stalled->count()} stalled lead(s).");

        return self::SUCCESS;
    }
}
