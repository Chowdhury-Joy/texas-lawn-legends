<?php

namespace App\Support\Niche;

use App\Models\Setting;
use App\Support\Niche\Packs\LawnPack;
use App\Support\Trial\TrialWorkspaceContext;
use InvalidArgumentException;

final class NicheResolver
{
    private static ?NichePack $resolved = null;

    public static function active(): NichePack
    {
        if (self::$resolved !== null) {
            return self::$resolved;
        }

        $id = self::activeId();
        $packs = config('niche.packs', []);

        if (! isset($packs[$id]) || ! is_string($packs[$id])) {
            throw new InvalidArgumentException("Unknown niche pack [{$id}].");
        }

        $class = $packs[$id];
        $pack = app($class);

        if (! $pack instanceof NichePack) {
            throw new InvalidArgumentException("Niche pack [{$id}] must implement NichePack.");
        }

        return self::$resolved = $pack;
    }

    /**
     * Setting override (demo loads) wins over APP_NICHE / config.
     */
    public static function activeId(): string
    {
        $workspace = TrialWorkspaceContext::current();

        if ($workspace !== null) {
            return $workspace->niche_id;
        }

        $fromSetting = setting('active_niche');

        if (filled($fromSetting)) {
            return (string) $fromSetting;
        }

        return (string) config('niche.active', 'lawn');
    }

    public static function demoMode(): bool
    {
        $workspace = TrialWorkspaceContext::current();

        if ($workspace !== null) {
            return (bool) $workspace->demo_mode;
        }

        return filter_var(setting('demo_mode', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function demoHubEnabled(): bool
    {
        return (bool) config('niche.demo_hub', false);
    }

    /**
     * Resolve a vocabulary label from the active pack.
     */
    public static function label(string $key, ?string $fallback = null): string
    {
        $labels = self::active()->labels();

        return $labels[$key] ?? $fallback ?? $key;
    }

    /**
     * Clear cached pack (tests / niche switch mid-request).
     */
    public static function flush(): void
    {
        self::$resolved = null;
    }

    /**
     * Default pack when config is missing (install safety).
     */
    public static function fallback(): NichePack
    {
        return new LawnPack;
    }
}
