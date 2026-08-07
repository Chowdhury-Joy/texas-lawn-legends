<?php

namespace App\Support\Trial;

use App\Models\TrialWorkspace;
use App\Models\Setting;
use App\Support\Niche\NicheResolver;
use Closure;

/**
 * Request-scoped binding for the active trial workspace (V3 Step 2).
 */
final class TrialWorkspaceContext
{
    private static ?TrialWorkspace $current = null;

    public static function set(?TrialWorkspace $workspace): void
    {
        self::$current = $workspace;
        Setting::flushRequestCache();
        NicheResolver::flush();
    }

    public static function current(): ?TrialWorkspace
    {
        return self::$current;
    }

    public static function id(): ?int
    {
        return self::$current?->id;
    }

    public static function clear(): void
    {
        self::set(null);
    }

    /**
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public static function run(TrialWorkspace $workspace, Closure $callback): mixed
    {
        $previous = self::$current;

        self::set($workspace);

        try {
            return $callback();
        } finally {
            self::set($previous);
        }
    }
}
