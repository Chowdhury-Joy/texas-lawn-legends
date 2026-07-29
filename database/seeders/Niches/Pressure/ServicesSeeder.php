<?php

namespace Database\Seeders\Niches\Pressure;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['Driveway & Walkway Wash', 'Oil stains, grime, and mildew lifted from concrete and pavers.', 'heroicon-o-home', 1.25],
            ['House & Siding Wash', 'Soft-wash safe clean for vinyl, brick, and stucco exteriors.', 'heroicon-o-building-office-2', 1.40],
            ['Patio & Pool Deck Wash', 'Slippery algae and dirt removed from outdoor living surfaces.', 'heroicon-o-sun', 1.35],
        ];

        $maintain = [
            ['Quarterly Home Wash', 'Seasonal siding and soffit refresh to prevent buildup.', 'heroicon-o-calendar-days', 0.85],
            ['Monthly Storefront Wash', 'Keep retail and restaurant fronts spotless for foot traffic.', 'heroicon-o-building-storefront', 0.95],
            ['HOA Common-Area Wash', 'Scheduled cleans for sidewalks, signs, and entry monuments.', 'heroicon-o-user-group', 1.05],
        ];

        $sort = 0;

        foreach ($projects as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' Crew arrives with commercial-grade equipment.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }

        foreach ($maintain as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Care,
                    'short_description' => $desc,
                    'long_description' => $desc.' Flexible reschedule via your confirmation link.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }
    }
}
