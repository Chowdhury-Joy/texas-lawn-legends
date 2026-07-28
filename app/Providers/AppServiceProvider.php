<?php

namespace App\Providers;

use App\Livewire\Hooks\CaptureFilamentPageHeading;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Site-wide content is resolved on demand in views via the setting()
     * and setting_image() helpers, which cache each key individually.
     */
    public function boot(): void
    {
        // Capture each Filament page heading before its layout renders, so the
        // topbar logo can show e.g. "Edit About Us" instead of the site name.
        Livewire::componentHook(CaptureFilamentPageHeading::class);
    }
}
