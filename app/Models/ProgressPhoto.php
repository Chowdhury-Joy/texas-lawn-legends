<?php

namespace App\Models;

use App\Models\Traits\TriggersSiteReload;
use Database\Factories\ProgressPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressPhoto extends Model
{
    /** @use HasFactory<ProgressPhotoFactory> */
    use HasFactory, TriggersSiteReload;

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'image_path',
        'caption',
        'milestone_step',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
