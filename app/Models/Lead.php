<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'address',
        'neighborhood',
        'estimated_sqft',
        'service_type',
        'calculated_estimate_low',
        'calculated_estimate_high',
        'step_reached',
        'status',
        'scheduled_at',
        'escalated_at',
        'external_booking_id',
    ];

    protected function casts(): array
    {
        return [
            'estimated_sqft' => 'integer',
            'calculated_estimate_low' => 'decimal:2',
            'calculated_estimate_high' => 'decimal:2',
            'status' => LeadStatus::class,
            'scheduled_at' => 'datetime',
            'escalated_at' => 'datetime',
        ];
    }

    public function scopeStalled($query, int $minutes)
    {
        return $query
            ->where('status', LeadStatus::Qualified)
            ->whereNull('scheduled_at')
            ->whereNull('escalated_at')
            ->where('updated_at', '<=', now()->subMinutes($minutes));
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            if (empty($lead->uuid)) {
                $lead->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }
}
