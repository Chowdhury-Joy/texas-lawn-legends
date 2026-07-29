<?php

namespace Database\Seeders\Niches\Fence;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $install = [
            ['Cedar Privacy Fence', 'Board-on-board cedar privacy fence with posts and cap.', 'heroicon-o-home', 1.40],
            ['Custom Gate Build', 'Single or double drive gates matched to existing fence.', 'heroicon-o-lock-closed', 1.35],
            ['Composite Fence Install', 'Low-maintenance composite panels for long-term durability.', 'heroicon-o-building-office-2', 1.50],
        ];

        $repair = [
            ['Board & Pickets Replacement', 'Swap storm-damaged or rotted sections without full tear-out.', 'heroicon-o-wrench-screwdriver', 0.90],
            ['Deck Board & Rail Repair', 'Replace deck boards, rails, and loose fasteners.', 'heroicon-o-squares-2x2', 0.95],
            ['Fence Stain & Seal', 'Clean, stain, and seal existing cedar or pine fences.', 'heroicon-o-paint-brush', 0.85],
        ];

        $sort = 0;

        foreach ($install as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' HOA-friendly layouts available on request.',
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
