<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Support\AccessPermissions;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * @return HasMany<Permission, $this>
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Whether this user may access a given admin-panel area.
     *
     * Admins are always superusers (cannot be locked out). Everyone else
     * starts from their role's default grants and is then modified by any
     * explicit per-user permission rows (which may grant or revoke).
     */
    public function canAccessKey(string $key): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->role instanceof UserRole) {
            return false;
        }

        $granted = in_array($key, AccessPermissions::defaultsFor($this->role), true);

        $override = $this->permissions
            ->where('key', $key)
            ->first();

        if ($override) {
            return (bool) $override->allowed;
        }

        return $granted;
    }

    /**
     * The set of access keys this user effectively holds right now,
     * used to pre-check the Access matrix in the User form.
     *
     * @return array<int, string>
     */
    public function effectiveKeys(): array
    {
        return collect(AccessPermissions::all())
            ->keys()
            ->filter(fn (string $key) => $this->canAccessKey($key))
            ->values()
            ->all();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role !== null;
    }
}
