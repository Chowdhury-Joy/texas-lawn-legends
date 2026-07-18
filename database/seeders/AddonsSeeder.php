<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Pre-Emergent Weed Application', 'Seasonal barrier treatment applied across turf and beds to stop weeds before they germinate.', 89.00, 'per application'],
            ['Premium Mulch Refresh', 'Fresh organic mulch layered to retain hydration and suppress weed growth across your beds.', 0.65, 'per dynamic sqft'],
            ['Seasonal Color Rotation', 'Hand-selected seasonal annuals installed and refreshed to keep beds vivid year-round.', 145.00, 'per application'],
            ['Irrigation Tune-Up', 'Full system inspection, head adjustment, and water conservation calibration.', 129.00, 'per application'],
            ['Storm Debris Cleanup', 'Priority removal of storm-dropped limbs and organic debris from the full property.', 0.18, 'per dynamic sqft'],
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
