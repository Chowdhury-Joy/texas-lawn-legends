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

        return Storage::disk('public')->url($path);
    }
}
