<?php

namespace App\Filament\Concerns;

use App\Models\User;
use App\Support\ProductFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Gates a Filament resource on the current user's effective access keys.
 *
 * Which roles reach a resource by default is declared centrally in
 * App\Support\AccessPermissions — not here — so that enforcement and the
 * per-user Access matrix in the User form always read from one registry.
 * Override permissionKey() only when a resource's key can't be derived
 * from its class name.
 */
trait RoleRestricted
{
    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $key = static::permissionKey();

        return $user->canAccessKey($key) && ProductFeatures::allows($key);
    }

    /**
     * Stable access key for this resource in the permission registry.
     * Defaults to "resource.{snake(short-class-name)}" (e.g. LeadsResource
     * -> resource.leads). Override on a resource only if it differs.
     */
    public static function permissionKey(): string
    {
        $short = (new \ReflectionClass(static::class))->getShortName();
        $short = preg_replace('/Resource$/', '', $short);

        return 'resource.'.Str::plural(Str::snake($short));
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }
}
