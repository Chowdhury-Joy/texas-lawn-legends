<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Only users with an assigned role may access the Filament admin panel.
        Gate::define('viewFilament', function (User $user) {
            return $user->role !== null;
        });
    }
}
