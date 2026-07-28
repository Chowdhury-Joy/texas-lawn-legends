<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active niche pack
    |--------------------------------------------------------------------------
    |
    | One install = one industry. Packs supply vocabulary + starter content.
    | Branding (logo, phone, colors) stays in admin settings.
    |
    */

    'active' => env('APP_NICHE', 'lawn'),

    /*
    |--------------------------------------------------------------------------
    | Registered packs
    |--------------------------------------------------------------------------
    */

    'packs' => [
        'lawn' => \App\Support\Niche\Packs\LawnPack::class,
    ],

];
