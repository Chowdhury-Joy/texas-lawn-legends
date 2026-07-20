<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Raw settings-key access.
 *
 * Delegates to the central permission registry rather than testing the role
 * directly, so this policy and the per-user Access matrix can never disagree.
 * Only admins hold `resource.settings` by default; an admin may grant it.
 */
class SettingPolicy
{
    use HandlesAuthorization;

    private const KEY = 'resource.settings';

    public function viewAny(User $user): bool
    {
        return $user->canAccessKey(self::KEY);
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->canAccessKey(self::KEY);
    }

    public function create(User $user): bool
    {
        return $user->canAccessKey(self::KEY);
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->canAccessKey(self::KEY);
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->canAccessKey(self::KEY);
    }

    public function deleteAny(User $user): bool
    {
        return $user->canAccessKey(self::KEY);
    }
}
