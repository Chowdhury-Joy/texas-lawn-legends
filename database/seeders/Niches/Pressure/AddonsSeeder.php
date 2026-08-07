<?php

namespace Database\Seeders\Niches\Pressure;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Fence Line Wash', 'Pressure wash along fence lines and gates.', 75.00, 'per application'],
            ['Gutter Brightening', 'Exterior gutter face wash paired with your house wash.', 95.00, 'per application'],
            ['Oil Spot Treatment', 'Heavy degreaser pass on stubborn driveway stains.', 0.12, 'per dynamic sqft'],
            ['Roof Soft-Wash Add-On', 'Low-pressure algae treatment for shaded roof sections.', 0.15, 'per dynamic sqft'],
        ];

        foreach ($addons as [$title, $desc, $price, $unit]) {
            Addon::query()->updateOrCreate(
                ['title' => $title],
                [
                    'description' => $desc,
                    'base_price' => $price,
                    'price_unit' => $unit,
                    'is_available' => true,
                ],
            );
        }
    }
}
