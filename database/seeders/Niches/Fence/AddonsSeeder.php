<?php

namespace Database\Seeders\Niches\Fence;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['Post Cap Upgrade', 'Decorative caps on all fence posts.', 8.00, 'per dynamic sqft'],
            ['Automatic Gate Opener', 'Motor and remote kit for existing drive gate.', 850.00, 'per application'],
            ['Pet Run Section', 'Small-gap pickets for dedicated dog run area.', 12.00, 'per dynamic sqft'],
            ['Deck Stain Refresh', 'Clean and stain entire deck surface.', 0.18, 'per dynamic sqft'],
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
