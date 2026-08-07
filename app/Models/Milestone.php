<?php

namespace App\Models;

use App\Models\Traits\BelongsToTrialWorkspace;

use App\Enums\MilestoneStatus;
use Database\Factories\MilestoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Milestone extends Model
{
    /** @use HasFactory<MilestoneFactory> */
    use BelongsToTrialWorkspace, HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MilestoneStatus::class,
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Keep `completed_at` in step with `status` so staff never have to set a
     * date by hand. An explicitly supplied date always wins (seeders stagger
     * demo dates), and reopening a step clears the date rather than leaving a
     * stale completion showing on the client timeline.
     */
    protected static function booted(): void
    {
        static::saving(function (self $milestone): void {
            if ($milestone->status === MilestoneStatus::Completed) {
                $milestone->completed_at ??= now();

                return;
            }

            $milestone->completed_at = null;
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
