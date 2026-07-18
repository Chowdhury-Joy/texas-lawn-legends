<?php

namespace App\Filament\Concerns;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

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

    public static function canView(Model $record): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return in_array($user->role, static::allowedRoles(), true);
    }
}
