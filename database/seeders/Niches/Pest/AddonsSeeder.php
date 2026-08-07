<?php

namespace Database\Seeders\Niches\Pest;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Attic Dusting', 'Light dust treatment in accessible attic spaces.', 75.00, 'per application'],
            ['Garage Perimeter Boost', 'Extra barrier treatment around garage doors and storage.', 45.00, 'per application'],
            ['Fire Ant Mound Treatment', 'Targeted mound treatment for yard hotspots.', 55.00, 'per application'],
            ['Termite Monitoring Stations', 'Install monitoring stations along foundation.', 0.06, 'per dynamic sqft'],
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
