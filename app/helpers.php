<?php

use App\Enums\ProductPart;
use App\Models\Setting;
use App\Support\ColorContrast;
use App\Support\Niche\NichePack;
use App\Support\Niche\NicheResolver;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /**
     * Resolve a CMS-managed setting value by key.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('contrast_ink')) {
    /**
     * Near-black or near-white ink for text sitting on the given background hex.
     */
    function contrast_ink(string $backgroundHex): string
    {
        return ColorContrast::inkOn($backgroundHex);
    }
}

if (! function_exists('accent_ink')) {
    /**
     * Accent color when readable on a surface; otherwise the surface's auto ink.
     */
    function accent_ink(string $accentHex, string $surfaceHex): string
    {
        return ColorContrast::accentInk($accentHex, $surfaceHex);
    }
}

if (! function_exists('body_ink')) {
    /**
     * Dark body/caption ink for white surfaces. Rejects light CMS slate picks.
     */
    function body_ink(string $hex, string $fallback = ColorContrast::DEFAULT_SLATE): string
    {
        return ColorContrast::bodyInk($hex, $fallback);
    }
}

if (! function_exists('product_part')) {
    /**
     * Active product package level (1 = Website+CMS, 2 = +Booking, 3 = +Ops).
     */
    function product_part(): ProductPart
    {
        $value = (int) setting('product_part', ProductPart::Ops->value);

        return ProductPart::tryFrom($value) ?? ProductPart::Ops;
    }
}

if (! function_exists('product_part_at_least')) {
    /**
     * Whether the install's product part is at or above the given level.
     */
    function product_part_at_least(ProductPart|int $minimum): bool
    {
        $required = $minimum instanceof ProductPart
            ? $minimum
            : (ProductPart::tryFrom($minimum) ?? ProductPart::Ops);

        return product_part()->atLeast($required);
    }
}

if (! function_exists('niche')) {
    /**
     * Active industry pack for this install.
     */
    function niche(): NichePack
    {
        return NicheResolver::active();
    }
}

if (! function_exists('niche_label')) {
    /**
     * Vocabulary string from the active niche pack.
     */
    function niche_label(string $key, ?string $fallback = null): string
    {
        return NicheResolver::label($key, $fallback);
    }
}

if (! function_exists('niche_favicon')) {
    /**
     * Browser-tab icon: the uploaded branding favicon when one exists, otherwise
     * the shipped icon for the active industry pack (public/images/favicons).
     */
    function niche_favicon(): string
    {
        $uploaded = setting_image('favicon');

        if ($uploaded) {
            return $uploaded;
        }

        $id = niche()->id();

        // A pack registered without a shipped icon falls back to the lawn mark
        // rather than emitting a 404 <link rel="icon">.
        if (! is_file(public_path("images/favicons/{$id}.svg"))) {
            $id = 'lawn';
        }

        return asset("images/favicons/{$id}.svg");
    }
}

if (! function_exists('setting_image')) {
    /**
     * Resolve a public URL for an image stored against a setting key.
     */
    function setting_image(string $key, ?string $default = null): ?string
    {
        $path = Setting::get($key);

        if (blank($path)) {
            return $default;
        }

        // Already an absolute URL (external asset) — return as-is.
        if (str_starts_with((string) $path, 'http://') || str_starts_with((string) $path, 'https://')) {
            return $path;
        }

        return public_url($path);
    }
}

if (! function_exists('public_url')) {
    /**
     * Resolve a root-relative public URL for a file on the "public" disk.
     *
     * Unlike Storage::disk('public')->url(), this ignores APP_URL so the
     * asset resolves against the host the visitor is actually on (avoids
     * broken images when APP_URL's port/host differs from the dev server).
     */
    function public_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        // Already an absolute URL (external asset) — return as-is.
        if (str_starts_with((string) $path, 'http://') || str_starts_with((string) $path, 'https://')) {
            return $path;
        }

        return '/storage/'.ltrim($path, '/');
    }
}
