<?php

namespace App\Filament\Concerns;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Lets a Filament resource restrict visibility to specific user roles.
 * Override allowedRoles() on the resource (defaults to all staff roles).
 */
trait RoleRestricted
{
    /**
     * @return array<int, UserRole>
     */
    public static function allowedRoles(): array
    {
        return [
            UserRole::Admin,
            UserRole::Content,
            UserRole::Operations,
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->canAccessKey(static::permissionKey());
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
