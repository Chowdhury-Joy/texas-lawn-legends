<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
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

    public static function home(): self
    {
        return static::query()->firstOrCreate(
            ['is_home' => true],
            ['title' => 'Home', 'blocks' => [], 'is_published' => true],
        );
    }
}
