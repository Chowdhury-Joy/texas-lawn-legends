<?php

namespace App\Models;

use App\Models\Traits\TriggersSiteReload;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory, TriggersSiteReload;

    protected $fillable = [
        'author',
        'neighborhood',
        'rating',
        'review_text',
        'service_tag',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeForNeighborhood(Builder $query, string $neighborhood): Builder
    {
        return $query->where('neighborhood', $neighborhood);
    }
}
