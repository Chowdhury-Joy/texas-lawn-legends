<?php

namespace App\Support\Niche;

use App\Support\Niche\Packs\LawnPack;
use InvalidArgumentException;

final class NicheResolver
{
    private static ?NichePack $resolved = null;

    public static function active(): NichePack
    {
        if (self::$resolved !== null) {
            return self::$resolved;
        }

        $id = (string) config('niche.active', 'lawn');
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
