<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\Niche\NicheResolver;
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
        foreach (NicheResolver::active()->contentSeeders() as $seeder) {
            $this->call([$seeder]);
        }

        $this->call([
            AccessCodesSeeder::class,
        ]);
    }
}
