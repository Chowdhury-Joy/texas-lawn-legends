<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\Niche\NicheResolver;
use Database\Seeders\Niches\Lawn\LawnNicheSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'TLL Administrator',
                'password' => Hash::make('pass'),
                'role' => UserRole::Admin,
            ],
        );

        $this->call([
            SettingsSeeder::class,
        ]);

        // Industry starter kit for the active APP_NICHE pack.
        $nicheId = NicheResolver::active()->id();
        if ($nicheId === 'lawn') {
            $this->call([LawnNicheSeeder::class]);
        }

        $this->call([
            AccessCodesSeeder::class,
        ]);
    }
}
