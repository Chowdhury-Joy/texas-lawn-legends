<?php

namespace App\Filament\Concerns;

use App\Models\User;

/**
 * Gates a dashboard widget on an access key, so widgets can't leak data from
 * a resource the viewer is blocked from opening. Override widgetPermissionKey()
 * to point at whichever resource backs the widget.
 */
trait RestrictedWidget
{
    public static function canView(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user?->canAccessKey(static::widgetPermissionKey()) ?? false;
    }

    protected static function widgetPermissionKey(): string
    {
        return 'resource.leads';
    }
}
