<?php

namespace Database\Seeders\Niches\Roofing;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Attic Ventilation Upgrade', 'Improve ridge and soffit airflow for shingle life.', 450.00, 'per application'],
            ['Ice & Water Shield Extra', 'Additional membrane in valleys and eaves.', 8.50, 'per dynamic sqft'],
            ['Gutter Guard Install', 'Keep debris out of new or existing gutters.', 12.00, 'per dynamic sqft'],
            ['Satellite/Dish Re-Mount', 'Safe reattach after tear-off.', 175.00, 'per application'],
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
