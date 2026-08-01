<?php

namespace App\Support;

/**
 * Auto ink for brand surfaces: only near-black or near-white, never admin-picked.
 */
final class ColorContrast
{
    public const NEAR_BLACK = '#1a1a1a';

    public const NEAR_WHITE = '#f2f2f2';

    /** Minimum contrast ratio before accent-as-ink is considered readable on a surface. */
    private const ACCENT_INK_MIN_RATIO = 3.0;

    /** WCAG AA for normal text on white (body / caption ink). */
    private const BODY_INK_MIN_RATIO = 4.5;

    public const DEFAULT_SLATE = '#334155';

    /**
     * Near-black or near-white ink that reads on the given background.
     */
    public static function inkOn(string $backgroundHex): string
    {
        $bg = self::relativeLuminance($backgroundHex);

        $blackContrast = self::contrastRatioFromLuminance($bg, self::relativeLuminance(self::NEAR_BLACK));
        $whiteContrast = self::contrastRatioFromLuminance($bg, self::relativeLuminance(self::NEAR_WHITE));

        return $whiteContrast >= $blackContrast ? self::NEAR_WHITE : self::NEAR_BLACK;
    }

    /**
     * Keep CMS "structural slate" dark enough to use as body text on white.
     * Light picks (often mistaken for a soft UI fill) fall back to the default slate.
     */
    public static function bodyInk(string $hex, string $fallback = self::DEFAULT_SLATE): string
    {
        $normalized = self::normalizeHex($hex);
        $safeFallback = self::normalizeHex($fallback) ?? self::DEFAULT_SLATE;

        if ($normalized === null) {
            return $safeFallback;
        }

        if (self::contrastRatio($normalized, '#ffffff') >= self::BODY_INK_MIN_RATIO) {
            return $normalized;
        }

        return $safeFallback;
    }

    /**
     * Prefer accent as decorative ink on a surface; fall back to surface ink if too weak.
     */
    public static function accentInk(string $accentHex, string $surfaceHex): string
    {
        if (self::contrastRatio($accentHex, $surfaceHex) >= self::ACCENT_INK_MIN_RATIO) {
            return self::normalizeHex($accentHex) ?? $accentHex;
        }

        return self::inkOn($surfaceHex);
    }

    public static function contrastRatio(string $hexA, string $hexB): float
    {
        return self::contrastRatioFromLuminance(
            self::relativeLuminance($hexA),
            self::relativeLuminance($hexB),
        );
    }

    public static function relativeLuminance(string $hex): float
    {
        $rgb = self::hexToRgb($hex);

        if ($rgb === null) {
            return 0.0;
        }

        [$r, $g, $b] = array_map(function (int $channel): float {
            $c = $channel / 255;

            return $c <= 0.03928
                ? $c / 12.92
                : (($c + 0.055) / 1.055) ** 2.4;
        }, $rgb);

        return (0.2126 * $r) + (0.7152 * $g) + (0.0722 * $b);
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    public static function hexToRgb(string $hex): ?array
    {
        $normalized = self::normalizeHex($hex);

        if ($normalized === null) {
            return null;
        }

        return [
            hexdec(substr($normalized, 1, 2)),
            hexdec(substr($normalized, 3, 2)),
            hexdec(substr($normalized, 5, 2)),
        ];
    }

    public static function normalizeHex(string $hex): ?string
    {
        $hex = trim($hex);

        if (preg_match('/^#([0-9a-fA-F]{3})$/', $hex, $m) === 1) {
            $short = $m[1];

            return '#'.strtolower($short[0].$short[0].$short[1].$short[1].$short[2].$short[2]);
        }

        if (preg_match('/^#([0-9a-fA-F]{6})$/', $hex) === 1) {
            return strtolower($hex);
        }

        return null;
    }

    private static function contrastRatioFromLuminance(float $l1, float $l2): float
    {
        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }
}
