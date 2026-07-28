<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
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
            ServicesSeeder::class,
            TestimonialsSeeder::class,
            AddonsSeeder::class,
            AccessCodesSeeder::class,
            SampleProjectSeeder::class,
            PagesSeeder::class,
        ]);
    }
}
