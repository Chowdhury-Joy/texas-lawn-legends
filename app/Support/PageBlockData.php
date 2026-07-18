<?php

namespace App\Support;

use App\Models\Service;
use App\Models\Testimonial;

/**
 * Data needed by the live-data blocks (service_matrix, neighborhood_proof) —
 * shared by the public PageController and the admin PageBuilder editor so
 * previews always match what the public site renders.
 */
class PageBlockData
{
    public static function live(): array
    {
        $createServices = Service::query()->active()->createSuite()->ordered()->get();
        $careServices = Service::query()->active()->careSuite()->ordered()->get();

        $testimonials = Testimonial::query()->featured()->latest()->get();

        $neighborhoods = collect(['Kessler Park', 'Bishop Arts', 'Highland Park', 'University Park', 'Oak Lawn'])
            ->merge($testimonials->pluck('neighborhood'))
            ->unique()
            ->values();

        return compact('createServices', 'careServices', 'testimonials', 'neighborhoods');
    }
}
