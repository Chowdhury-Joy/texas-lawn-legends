<?php

namespace Database\Seeders\Niches\Cleaning;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Inside Oven Clean', 'Full oven interior degrease and polish.', 65.00, 'per application'],
            ['Inside Fridge Clean', 'Shelves, drawers, and seals wiped and sanitized.', 55.00, 'per application'],
            ['Interior Window Detail', 'Interior glass and tracks for living areas.', 0.08, 'per dynamic sqft'],
            ['Laundry Fold Add-On', 'Wash, dry, and fold while we clean.', 45.00, 'per application'],
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
