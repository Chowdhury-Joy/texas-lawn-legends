<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends high-priority operational alerts to the dispatcher — currently a
 * configurable outbound webhook plus an application log entry. Used for
 * add-on orders and (later) stalled-lead escalation.
 */
class OperationsNotifier
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function dispatch(string $subject, array $payload): void
    {
        Log::info('[OPERATIONS ALERT] '.$subject, $payload);

        $webhook = trim((string) setting('operations_webhook_url'));

        if ($webhook === '') {
            return;
        }

        try {
            Http::timeout(5)->acceptJson()->post($webhook, [
                'subject' => $subject,
                'data' => $payload,
                'sent_at' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Operations webhook failed: '.$e->getMessage());
        }
    }
}
