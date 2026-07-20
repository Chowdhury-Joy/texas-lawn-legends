<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'neighborhood', 'service_type', 'status', 'scheduled_at', 'escalated_at'])
            ->logOnlyDirty()
            ->useLogName('lead');
    }

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

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
