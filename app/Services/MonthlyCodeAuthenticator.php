<?php

namespace App\Services;

use App\Models\AccessCode;

/**
 * Validates monthly member access tokens against the access_codes table and
 * maintains the unlocked portal state in the session.
 *
 * A code is only valid during the calendar month it was generated for
 * (target_month = 'YYYY-MM'), so access naturally expires each month.
 */
class MonthlyCodeAuthenticator
{
    public const SESSION_KEY = 'portal.access';

    /**
     * Attempt to unlock the portal with a token. Returns true on success and
     * writes a protected session variable that keeps the portal open.
     */
    public function attempt(string $code): bool
    {
        $record = AccessCode::query()
            ->where('code', trim($code))
            ->active()
            ->first();

        if (! $record) {
            return false;
        }

        if ($record->target_month !== $this->currentMonth()) {
            return false;
        }

        // Increment the access log.
        $record->increment('usage_count');

        session([self::SESSION_KEY => [
            'code' => $record->code,
            'client_id' => $record->client_id_restriction,
            'month' => $record->target_month,
            'unlocked_at' => now()->toIso8601String(),
        ]]);

        return true;
    }

    /**
     * Is the portal currently unlocked for a code valid this month?
     */
    public function unlocked(): bool
    {
        $state = session(self::SESSION_KEY);

        return is_array($state) && ($state['month'] ?? null) === $this->currentMonth();
    }

    /**
     * The protected session context (code, client_id, month) when unlocked.
     *
     * @return array<string, mixed>|null
     */
    public function context(): ?array
    {
        return $this->unlocked() ? session(self::SESSION_KEY) : null;
    }

    public function lock(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    protected function currentMonth(): string
    {
        return now()->format('Y-m');
    }
}
