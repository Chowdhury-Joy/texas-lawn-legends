<?php

namespace App\Livewire\Hooks;

use App\Support\FilamentPageHeading;
use Livewire\ComponentHook;

use function Livewire\on;

class CaptureFilamentPageHeading extends ComponentHook
{
    public static function provide(): void
    {
        // Registered via provide() so it works even when this hook is added
        // after ComponentHookRegistry::boot() has already run.
        on('render', function ($component, $view, $data): void {
            FilamentPageHeading::remember($component);
        });
    }
}
