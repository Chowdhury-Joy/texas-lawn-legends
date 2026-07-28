<?php

namespace Database\Seeders\Niches\Roofing;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $install = [
            ['Full Tear-Off & Install', 'Remove existing layers and install architectural shingles with ridge vent and warranty.', 'heroicon-o-home-modern', 1.50],
            ['Metal Roof Install', 'Standing-seam or panel metal systems built for Texas heat and hail.', 'heroicon-o-square-3-stack-3d', 1.70],
            ['Roof Upgrade Overlay', 'Add a new layer where structure allows — faster timeline, solid protection.', 'heroicon-o-squares-2x2', 1.25],
        ];

        $repair = [
            ['Storm Damage Repair', 'Shingle replacement, flashing, and leak mitigation after hail or wind.', 'heroicon-o-bolt', 1.10],
            ['Leak Diagnosis & Patch', 'Find the source, dry-in, and patch with matching materials.', 'heroicon-o-magnifying-glass', 0.95],
            ['Gutter & Flashing Tune-Up', 'Secure edges, clear pathways, and reseal vulnerable joints.', 'heroicon-o-wrench-screwdriver', 0.85],
        ];

        $sort = 0;

        foreach ($install as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' Includes cleanup and magnetic nail sweep.',
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
                    'long_description' => $desc.' Photo documentation in your client dashboard.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }
    }
}
