<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialWorkspace extends Model
{
    protected $fillable = [
        'slug',
        'niche_id',
        'owner_user_id',
        'expires_at',
        'product_part',
        'demo_mode',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'demo_mode' => 'boolean',
            'product_part' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }

    public function expiresAt(): CarbonInterface
    {
        return $this->expires_at;
    }

    public function adminPath(): string
    {
        return 'trial/'.$this->slug.'/admin';
    }

    public function publicPath(string $path = ''): string
    {
        $base = 'trial/'.$this->slug;

        if ($path === '' || $path === '/') {
            return '/'.$base;
        }

        return '/'.$base.'/'.ltrim($path, '/');
    }
}
