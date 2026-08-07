<?php

namespace App\Models;

use App\Models\Traits\BelongsToTrialWorkspace;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;


class Crew extends Model
{
    use BelongsToTrialWorkspace, HasFactory;
    use LogsActivity;
    use SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'leader_name', 'phone', 'color', 'notes'])
            ->logOnlyDirty()
            ->useLogName('crew');
    }

    protected $fillable = [
        'name',
        'leader_name',
        'phone',
        'color',
        'notes',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Crew $crew) {
            $crew->projects()
                ->whereIn('status', [ProjectStatus::Scheduled, ProjectStatus::Active])
                ->update(['crew_id' => null]);
        });
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
