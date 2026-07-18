<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Models\User;
use App\Services\OperationsNotifier;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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

        $recipients = User::query()
            ->whereIn('role', [UserRole::Admin->value, UserRole::Operations->value])
            ->get();

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

            if ($recipients->isNotEmpty()) {
                $databaseNotification = Notification::make()
                    ->title('Stalled lead needs a follow-up call')
                    ->body(($lead->name ?: 'A lead').' in '.($lead->neighborhood ?: 'an unknown area').' has been sitting unbooked. Give them a call.')
                    ->icon('heroicon-o-phone-arrow-up-right')
                    ->warning()
                    ->actions([
                        Action::make('open')
                            ->label('Open lead')
                            ->url(LeadResource::getUrl('edit', ['record' => $lead]))
                            ->markAsRead(),
                    ])
                    ->toDatabase();

                // Deliver synchronously — this app runs on shared hosting without a queue worker.
                foreach ($recipients as $recipient) {
                    $recipient->notifyNow($databaseNotification);
                }
            }

            // Mark escalated without touching updated_at / firing model events again.
            $lead->forceFill(['escalated_at' => now()])->saveQuietly();
        }

        $this->info("Escalated {$stalled->count()} stalled lead(s).");

        return self::SUCCESS;
    }
}
