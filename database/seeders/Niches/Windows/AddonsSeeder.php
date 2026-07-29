<?php

namespace Database\Seeders\Niches\Windows;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Screen Cleaning', 'Remove, wash, and reinstall window screens.', 45.00, 'per application'],
            ['Skylight Detail', 'Interior and exterior skylight glass.', 65.00, 'per application'],
            ['Storm Door Glass', 'Detail glass on storm and patio doors.', 35.00, 'per application'],
            ['Gutter Face Wipe', 'Quick wipe of visible gutter faces while on ladder.', 55.00, 'per application'],
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
