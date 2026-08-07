<?php

namespace App\Models;

use App\Models\Traits\BelongsToTrialWorkspace;

use App\Models\Traits\TriggersSiteReload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Page extends Model
{
    use BelongsToTrialWorkspace;
    use TriggersSiteReload;

    protected $fillable = [
        'title',
        'slug',
        'is_home',
        'blocks',
        'seo_title',
        'seo_description',
        'seo_image',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_home' => 'boolean',
            'blocks' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * The homepage record, read-only — public page renders must never write.
     * Returns an unsaved instance if none exists yet (fresh installs before
     * PagesSeeder runs), so the site renders an empty homepage instead of
     * erroring or inserting a row mid-request.
     */
    public static function home(): self
    {
        return static::query()->where('is_home', true)->first()
            ?? new static(['title' => 'Home', 'blocks' => [], 'is_published' => true]);
    }

    /**
     * The homepage record, creating it if absent. For admin/seeder paths only.
     */
    public static function homeOrCreate(): self
    {
        return static::query()->firstOrCreate(
            ['is_home' => true],
            ['title' => 'Home', 'blocks' => [], 'is_published' => true],
        );
    }
}
