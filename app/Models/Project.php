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
        'unique_dashboard_hash',
        'client_name',
        'project_title',
        'neighborhood',
        'contract_value',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'contract_value' => 'decimal:2',
            'status' => ProjectStatus::class,
            'started_at' => 'date',
            'completed_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->unique_dashboard_hash)) {
                $project->unique_dashboard_hash = static::generateUniqueHash();
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

    public function getRouteKeyName(): string
    {
        return 'unique_dashboard_hash';
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
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
