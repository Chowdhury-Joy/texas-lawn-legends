<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['client_name', 'project_title', 'neighborhood', 'contract_value', 'status', 'started_at', 'completed_at'])
            ->logOnlyDirty()
            ->useLogName('project');
    }

    protected $fillable = [
        'lead_id',
        'crew_id',
        'unique_dashboard_hash',
        'referral_code',
        'client_name',
        'project_title',
        'neighborhood',
        'contract_value',
        'material_cost',
        'labor_cost',
        'status',
        'started_at',
        'completed_at',
        'review_requested_at',
    ];

    protected function casts(): array
    {
        return [
            'contract_value' => 'decimal:2',
            'material_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'status' => ProjectStatus::class,
            'started_at' => 'date',
            'completed_at' => 'date',
            'review_requested_at' => 'datetime',
        ];
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->material_cost + (float) $this->labor_cost;
    }

    public function getProfitMarginAttribute(): float
    {
        return (float) $this->contract_value - $this->total_cost;
    }

    public function getProfitMarginPercentAttribute(): ?float
    {
        $contract = (float) $this->contract_value;

        if ($contract <= 0) {
            return 0;
        }

        if ((float) $this->material_cost === 0.0 && (float) $this->labor_cost === 0.0) {
            return null;
        }

        return round(($this->profit_margin / $contract) * 100, 1);
    }

    public static function getProfitMarginSql(): string
    {
        return '
            CASE 
                WHEN contract_value <= 0 THEN 0 
                WHEN material_cost = 0 AND labor_cost = 0 THEN NULL 
                ELSE (((contract_value - material_cost - labor_cost) * 1.0) / contract_value) * 100 
            END
        ';
    }

    public function scopeWithProfitMargin($query)
    {
        return $query->selectRaw('*, ('.static::getProfitMarginSql().') as calculated_profit_margin');
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->unique_dashboard_hash)) {
                $project->unique_dashboard_hash = static::generateUniqueHash();
            }
            if (empty($project->referral_code)) {
                $project->referral_code = static::generateReferralCode();
            }
        });
    }

    public static function generateUniqueHash(): string
    {
        do {
            $hash = Str::lower(Str::random(32));
        } while (static::query()->where('unique_dashboard_hash', $hash)->exists());

        return $hash;
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (static::query()->where('referral_code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'unique_dashboard_hash';
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function progressPhotos(): HasMany
    {
        return $this->hasMany(ProgressPhoto::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
