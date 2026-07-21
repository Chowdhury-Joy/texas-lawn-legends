<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Cache;

trait TriggersSiteReload
{
    protected static function bootTriggersSiteReload()
    {
        static::saved(fn () => Cache::put('site_version', time()));
        static::deleted(fn () => Cache::put('site_version', time()));
    }
}
