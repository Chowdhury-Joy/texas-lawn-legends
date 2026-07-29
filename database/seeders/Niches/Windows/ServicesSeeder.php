<?php

namespace Database\Seeders\Niches\Windows;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['Move-Out Window Detail', 'Full interior and exterior clean for vacated homes.', 'heroicon-o-home', 1.35],
            ['Post-Construction Glass', 'Sticker, paint, and dust removal on new builds.', 'heroicon-o-wrench-screwdriver', 1.50],
            ['Hard-Water Stain Removal', 'Mineral deposit treatment for shower glass and exterior panes.', 'heroicon-o-sparkles', 1.45],
        ];

        $maintain = [
            ['Monthly Home Route', 'Exterior panes cleaned on a fixed monthly schedule.', 'heroicon-o-calendar-days', 0.88],
            ['Bi-Monthly Home Route', 'Every-other-month interior and exterior detail.', 'heroicon-o-calendar', 0.92],
            ['Storefront Window Route', 'Weekly or bi-weekly glass for retail and offices.', 'heroicon-o-building-storefront', 1.00],
        ];

        $sort = 0;

        foreach ($projects as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' Pure water and squeegee finish on every pane.',
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
