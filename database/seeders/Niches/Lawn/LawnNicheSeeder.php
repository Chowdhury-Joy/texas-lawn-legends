<?php

namespace Database\Seeders\Niches\Lawn;

use App\Support\Niche\NicheResolver;
use Illuminate\Database\Seeder;

/**
 * Lawn industry starter kit: services, testimonials, add-ons, sample project, CMS pages.
 */
class LawnNicheSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(NicheResolver::active()->contentSeeders());
    }
}
