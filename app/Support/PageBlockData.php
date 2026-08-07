<?php

namespace App\Support;

use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Support\Collection;

/**
 * Data needed by the live-data blocks (service_matrix, neighborhood_proof,
 * testimonial_grid, review_spotlight) — shared by the public PageController
 * and the admin page preview so previews always match the public site.
 *
 * Each set is memoised for the request, so the layout footer reuses the same
 * service collections the blocks were handed instead of re-querying them.
 *
 * Note live() still resolves all three up front because they are passed as
 * view data. Making a testimonial query conditional on a page actually having
 * a testimonial block would mean having the block templates pull from the
 * accessors rather than receiving variables — worth doing, but a wider change.
 */
class PageBlockData
{
    /** @var array<string, Collection<int, mixed>> */
    private static array $cache = [];

    /**
     * @return array<string, Collection<int, mixed>>
     */
    public static function live(): array
    {
        return [
            'createServices' => static::createServices(),
            'careServices' => static::careServices(),
            'testimonials' => static::testimonials(),
        ];
    }

    /**
     * @return Collection<int, Service>
     */
    public static function createServices(): Collection
    {
        return static::$cache['createServices'] ??= Service::query()->active()->createSuite()->ordered()->get();
    }

    /**
     * @return Collection<int, Service>
     */
    public static function careServices(): Collection
    {
        return static::$cache['careServices'] ??= Service::query()->active()->careSuite()->ordered()->get();
    }

    /**
     * @return Collection<int, Testimonial>
     */
    public static function testimonials(): Collection
    {
        return static::$cache['testimonials'] ??= Testimonial::query()->latest()->get();
    }

    /**
     * Drop the request-scoped cache. Needed between assertions in tests that
     * create records after a first read.
     */
    public static function flush(): void
    {
        static::$cache = [];
    }
}
