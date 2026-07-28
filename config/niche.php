<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active niche pack
    |--------------------------------------------------------------------------
    |
    | Install default from .env. Demo loads may override via the active_niche
    | setting without rewriting this file.
    |
    */

    'active' => env('APP_NICHE', 'lawn'),

    /*
    |--------------------------------------------------------------------------
    | Public demo hub
    |--------------------------------------------------------------------------
    |
    | When true (or APP_ENV=local), /demo is available for one-click pack loads.
    |
    */

    'demo_hub' => filter_var(
        env('APP_DEMO_HUB', env('APP_ENV') === 'local' ? 'true' : 'false'),
        FILTER_VALIDATE_BOOLEAN
    ),

    /*
    |--------------------------------------------------------------------------
    | Registered packs
    |--------------------------------------------------------------------------
    */

    'packs' => [
        'lawn' => \App\Support\Niche\Packs\LawnPack::class,
        'cleaning' => \App\Support\Niche\Packs\CleaningPack::class,
        'roofing' => \App\Support\Niche\Packs\RoofingPack::class,
    ],

];
