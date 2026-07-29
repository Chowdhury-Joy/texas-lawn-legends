<?php

namespace Database\Seeders\Niches\Pest;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            ['Whole-Home Perimeter Treatment', 'Interior and exterior barrier treatment for common pests.', 'heroicon-o-home', 1.30],
            ['Rodent Exclusion Visit', 'Inspect entry points, set traps, and seal small gaps.', 'heroicon-o-bug-ant', 1.45],
            ['Wasp & Nest Removal', 'Safe removal of active nests around eaves and patios.', 'heroicon-o-exclamation-triangle', 1.35],
        ];

        $protect = [
            ['Quarterly Shield Plan', 'Seasonal exterior barrier with interior touch-ups as needed.', 'heroicon-o-shield-check', 0.82],
            ['Bi-Monthly Protect Plan', 'More frequent visits for wooded-lot homes.', 'heroicon-o-calendar-days', 0.88],
            ['Mosquito Yard Treatment', 'Monthly yard barrier during peak mosquito season.', 'heroicon-o-sun', 0.95],
        ];

        $sort = 0;

        foreach ($treatments as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' Pet-safe product options available on request.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }

        foreach ($protect as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Care,
                    'short_description' => $desc,
                    'long_description' => $desc.' Free re-treat between visits if pests return.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }
    }
}
