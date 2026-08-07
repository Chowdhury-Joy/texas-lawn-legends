<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Licence track for this install
    |--------------------------------------------------------------------------
    |
    | 'a' = Track A "Own it" (one-time purchase; self-serve full data export).
    | 'b' = Track B "Rent it" (subscription on our hosting; export only with
    | Getwebfield super-admin consent, until a buy-out to Track A).
    |
    | Deliberately environment-driven, never a CMS setting — a rented install
    | must not be able to grant itself an export from the admin panel. Local
    | dev and sales demos default to Track A so the feature is walkable;
    | every other environment defaults to the locked-down track and must opt
    | in explicitly at handoff.
    |
    */

    'track' => env('APP_LICENSE_TRACK', env('APP_ENV') === 'local' ? 'a' : 'b'),

];
