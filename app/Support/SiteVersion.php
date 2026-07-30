<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Tracks the site content version that authenticated pages poll to auto-reload.
 *
 * Every CMS-ish model bumps this on save. Bulk operations (niche pack restore
 * seeds hundreds of rows) suppress the per-row bump and fire one at the end,
 * otherwise every open admin tab reloads repeatedly during a restore.
 */
final class SiteVersion
{
    private static bool $suppressed = false;

    public static function current(): int
    {
        return (int) Cache::get('site_version', 1);
    }

    public static function bump(): void
    {
        if (self::$suppressed) {
            return;
        }

        self::bumpNow();
    }

    public static function bumpNow(): void
    {
        Cache::put('site_version', time());
    }

    /**
     * Run a bulk operation with a single version bump instead of one per row.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function withoutBumping(callable $callback): mixed
    {
        $previous = self::$suppressed;
        self::$suppressed = true;

        try {
            return $callback();
        } finally {
            self::$suppressed = $previous;
        }
    }
}
