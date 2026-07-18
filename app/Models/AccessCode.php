<?php

namespace App\Models;

use Database\Factories\AccessCodeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    /** @use HasFactory<AccessCodeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'target_month',
        'client_id_restriction',
        'is_active',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'client_id_restriction' => 'integer',
            'is_active' => 'boolean',
            'usage_count' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope codes valid for a given YYYY-MM period (defaults to the current month).
     */
    public function scopeForMonth(Builder $query, ?string $month = null): Builder
    {
        return $query->where('target_month', $month ?? now()->format('Y-m'));
    }
}
