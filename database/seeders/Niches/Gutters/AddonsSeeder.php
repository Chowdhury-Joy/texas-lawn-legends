<?php

namespace Database\Seeders\Niches\Gutters;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Fascia Board Repair', 'Replace rotted fascia before new gutter hang.', 12.00, 'per dynamic sqft'],
            ['Rain Barrel Hookup', 'Connect downspout to existing rain barrel system.', 125.00, 'per application'],
            ['Extra Downspout Run', 'Additional downspout and extension beyond standard package.', 95.00, 'per application'],
            ['Ice Dam Prevention Kit', 'Heat cable prep for north-facing roof lines.', 8.50, 'per dynamic sqft'],
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
