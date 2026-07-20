<?php

namespace App\Models;

use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Proposal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_id',
        'project_id',
        'unique_token',
        'status',
        'total_amount',
        'content',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProposalStatus::class,
            'content' => 'json',
            'expires_at' => 'date',
            'accepted_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::creating(function (Proposal $proposal) {
            if (empty($proposal->unique_token)) {
                $proposal->unique_token = static::generateUniqueToken();
            }
        });

        static::saving(function (Proposal $proposal) {
            if (is_null($proposal->lead_id) && is_null($proposal->project_id)) {
                throw new \Exception('A proposal must have either a lead_id or a project_id.');
            }
        });
    }

    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('unique_token', $token)->exists());

        return $token;
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
