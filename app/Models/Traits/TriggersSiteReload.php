<?php

namespace App\Models\Traits;

use App\Support\SiteVersion;

trait TriggersSiteReload
{
    protected static function bootTriggersSiteReload()
    {
        static::saved(fn () => SiteVersion::bump());
        static::deleted(fn () => SiteVersion::bump());
    }
}
