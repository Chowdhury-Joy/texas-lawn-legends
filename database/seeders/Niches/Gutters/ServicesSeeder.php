<?php

namespace Database\Seeders\Niches\Gutters;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $install = [
            ['Seamless Gutter Replacement', 'Full tear-off and new 6" K-style seamless gutters with downspouts.', 'heroicon-o-home', 1.45],
            ['New Construction Gutters', 'Complete gutter package for new builds and additions.', 'heroicon-o-building-office-2', 1.35],
            ['Gutter Guard Install', 'Micro-mesh or screen guards paired with new or existing gutters.', 'heroicon-o-shield-check', 1.40],
        ];

        $repair = [
            ['Seasonal Gutter Clean-Out', 'Debris removal, flush, and downspout check.', 'heroicon-o-sparkles', 0.80],
            ['Leak & Slope Repair', 'Reseal joints, adjust pitch, and replace damaged sections.', 'heroicon-o-wrench-screwdriver', 0.95],
            ['Downspout Redirect', 'Extend or reroute downspouts away from foundation.', 'heroicon-o-arrow-trending-down', 0.85],
        ];

        $sort = 0;

        foreach ($install as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' Color-matched to your trim on install day.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }

        foreach ($repair as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Care,
                    'short_description' => $desc,
                    'long_description' => $desc.' Photo report included after every visit.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }
    }
}
