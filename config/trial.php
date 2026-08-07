<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trial host mode (V3 Step 1)
    |--------------------------------------------------------------------------
    |
    | When true, this install is a Getwebfield trial sandbox host: agency
    | homepage at / until provisioned, signup + niche picker, 15-day clock.
    | Keep APP_DEMO_HUB=false on trial hosts so Restore stays sales-only.
    | Use MySQL for app data on trial hosts (suggestions.md P-11).
    |
    | Step 2 (not built): isolated workspaces at /trial/{slug} so many
    | strangers can sign up without sharing one database.
    |
    */

    'enabled' => filter_var(env('APP_TRIAL_HOST', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Trial length
    |--------------------------------------------------------------------------
    */

    'duration_days' => (int) env('APP_TRIAL_DAYS', 15),

];
