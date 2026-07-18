<?php

namespace App\Models;

use App\Enums\ServiceCategory;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'short_description',
        'long_description',
        'icon',
        'image',
        'base_price_multiplier',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => ServiceCategory::class,
            'base_price_multiplier' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCreateSuite(Builder $query): Builder
    {
        return $query->where('category', ServiceCategory::Create);
    }

    public function scopeCareSuite(Builder $query): Builder
    {
        return $query->where('category', ServiceCategory::Care);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}
