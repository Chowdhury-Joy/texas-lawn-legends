<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// cPanel runs this via a single cron entry (`* * * * * php artisan schedule:run`),
// no persistent queue worker required.
Schedule::command('leads:escalate-stalled')->everyMinute()->withoutOverlapping();
