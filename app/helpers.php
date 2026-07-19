<?php

use App\Models\Setting;
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
