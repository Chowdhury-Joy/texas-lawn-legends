<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    protected $fillable = [
        'crew_id',
        'project_id',
        'clock_in_at',
        'clock_out_at',
        'hourly_rate',
        'calculated_cost',
    ];

    protected function casts(): array
    {
        return [
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'hourly_rate' => 'decimal:2',
            'calculated_cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (TimeEntry $entry) {
            if ($entry->clock_in_at && $entry->clock_out_at) {
                $hours = $entry->clock_in_at->diffInMinutes($entry->clock_out_at, true) / 60;
                $entry->calculated_cost = $hours * $entry->hourly_rate;
            } else {
                $entry->calculated_cost = 0;
            }
        });

        $updateProjectCost = function (TimeEntry $entry) {
            $project = $entry->project;
            if ($project && ! $project->use_manual_labor_cost) {
                $totalLaborCost = static::where('project_id', $project->id)->sum('calculated_cost');
                $project->updateQuietly(['labor_cost' => $totalLaborCost]);
            }
        };

        static::saved($updateProjectCost);
        static::deleted($updateProjectCost);
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
