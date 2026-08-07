<?php

namespace App\Models\Traits;

use App\Models\TrialWorkspace;
use App\Support\Trial\TrialWorkspaceContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 */
trait BelongsToTrialWorkspace
{
    public static function bootBelongsToTrialWorkspace(): void
    {
        static::addGlobalScope('trial_workspace', function (Builder $builder): void {
            $workspaceId = TrialWorkspaceContext::id();
            $column = $builder->getModel()->qualifyColumn('trial_workspace_id');

            if ($workspaceId !== null) {
                $builder->where($column, $workspaceId);
            } else {
                $builder->whereNull($column);
            }
        });

        static::creating(function (Model $model): void {
            if ($model->getAttribute('trial_workspace_id') === null && TrialWorkspaceContext::id() !== null) {
                $model->setAttribute('trial_workspace_id', TrialWorkspaceContext::id());
            }
        });
    }

    /**
     * @return BelongsTo<TrialWorkspace, $this>
     */
    public function trialWorkspace(): BelongsTo
    {
        return $this->belongsTo(TrialWorkspace::class);
    }

    public static function withoutTrialWorkspaceScope(): Builder
    {
        return static::query()->withoutGlobalScope('trial_workspace');
    }
}
