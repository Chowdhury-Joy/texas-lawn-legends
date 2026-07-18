<?php

namespace Database\Seeders;

use App\Models\AccessCode;
use Illuminate\Database\Seeder;

class AccessCodesSeeder extends Seeder
{
    public function run(): void
    {
        $month = now()->format('Y-m');

        $codes = [
            ['code' => 'LEGENDS-'.now()->format('ymd'), 'target_month' => $month],
            ['code' => 'MEMBER-'.strtoupper(now()->format('M')), 'target_month' => $month],
        ];

        foreach ($codes as $code) {
            AccessCode::query()->updateOrCreate(
                ['code' => $code['code']],
                [
                    'target_month' => $code['target_month'],
                    'is_active' => true,
                    'usage_count' => 0,
                ],
            );
        }
    }
}
