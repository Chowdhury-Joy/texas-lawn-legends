<?php

namespace Database\Seeders\Niches\Lawn;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $create = [
            ['Retaining Walls', 'Concrete, brick, and natural stone structures engineered for maximum soil stabilization and erosion prevention.', 'heroicon-o-square-3-stack-3d', 1.60],
            ['Custom Patios', 'Tailored outdoor extensions built using premium natural flagstone or structural pavers.', 'heroicon-o-squares-2x2', 1.45],
            ['Hardscape Architecture', 'Engineered layout plans integrating functional structural concrete paths and custom walkways.', 'heroicon-o-map', 1.50],
            ['Artificial Turf Installation', 'Durable, pet-safe, low-maintenance evergreen lawns built for year-round high-traffic use.', 'heroicon-o-sparkles', 1.35],
            ['Sod Laying', 'Rapid-rooting turf varieties selected explicitly for local Dallas climate resilience.', 'heroicon-o-rectangle-group', 1.20],
            ['2D & 3D Visual Modeling', 'Full immersive digital rendering pipelines allowing complete structural validation prior to installation.', 'heroicon-o-cube', 1.15],
        ];

        $care = [
            ['Precision Mowing', 'Weekly cutting operations, trimming, mechanical edging, and clean blow-offs.', 'heroicon-o-scissors', 0.85],
            ['Targeted Weed Mitigation', 'Proactive pre-emergent and post-emergent eradication applications.', 'heroicon-o-bug-ant', 0.80],
            ['Artisan Pruning', 'Specialized seasonal shaping for mature trees, ornamental shrubs, and hedges.', 'heroicon-o-scissors', 0.90],
            ['Seasonal Cleanup Operations', 'Thorough removal of organic leaf drops and storm debris.', 'heroicon-o-trash', 0.75],
            ['Premium Mulching', 'Layering organic mulch to retain soil hydration and prevent weed growth.', 'heroicon-o-beaker', 0.95],
            ['Irrigation System Auditing', 'Comprehensive inspections, water conservation checks, and hardware tune-ups.', 'heroicon-o-wrench-screwdriver', 1.00],
        ];

        $sort = 0;

        foreach ($create as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Create,
                    'short_description' => $desc,
                    'long_description' => $desc.' Every installation is engineered to premium spec and backed by our systematized delivery process.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }

        foreach ($care as [$title, $desc, $icon, $multiplier]) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => ServiceCategory::Care,
                    'short_description' => $desc,
                    'long_description' => $desc.' Delivered on a reliable schedule by our professional maintenance crew.',
                    'icon' => $icon,
                    'base_price_multiplier' => $multiplier,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ],
            );
        }
    }
}
