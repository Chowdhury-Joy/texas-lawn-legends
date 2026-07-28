<?php

namespace Database\Seeders\Niches\Cleaning;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['Move-In Deep Clean', 'Full top-to-bottom clean before you unpack — kitchen, baths, floors, and detail work.', 'heroicon-o-home', 1.40],
            ['Post-Construction Clean', 'Dust, debris, and finish wipe-downs after renovations or new builds.', 'heroicon-o-wrench-screwdriver', 1.55],
            ['One-Time Deep Clean', 'Cabinets, baseboards, appliances, and high-touch surfaces reset.', 'heroicon-o-sparkles', 1.30],
        ];

        $maintain = [
            ['Weekly Recurring Clean', 'Reliable weekly maintenance for busy households.', 'heroicon-o-calendar-days', 0.90],
            ['Bi-Weekly Recurring Clean', 'Every-other-week plan that keeps dust and kitchens under control.', 'heroicon-o-calendar', 0.85],
            ['Office Refresh', 'After-hours tidy for small offices and studios.', 'heroicon-o-building-office', 1.00],
        ];

        $sort = 0;

        foreach ($projects as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' Crew arrives stocked and on schedule.',
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
