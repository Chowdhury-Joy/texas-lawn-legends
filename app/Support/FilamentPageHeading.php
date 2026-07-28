<?php

namespace App\Support;

use Filament\Pages\BasePage;
use Filament\Pages\SimplePage;

class FilamentPageHeading
{
    public const REQUEST_KEY = 'filament_page_heading';

    /**
     * Remember the current panel page heading for this request
     * (so the topbar/sidebar logo can show it).
     */
    public static function remember(?object $livewire): void
    {
        if (! $livewire instanceof BasePage || $livewire instanceof SimplePage) {
            return;
        }

        $heading = $livewire->getHeading() ?? $livewire->getTitle();

        if (! filled($heading)) {
            return;
        }

        $label = trim(strip_tags((string) $heading));

        if ($label === '') {
            return;
        }

        request()->attributes->set(self::REQUEST_KEY, $label);
    }

    /**
     * Resolve the current Filament page heading for the topbar/sidebar logo.
     */
    public static function current(): ?string
    {
        $cached = request()->attributes->get(self::REQUEST_KEY);

        return filled($cached) ? (string) $cached : null;
    }
}
