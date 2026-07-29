<?php

namespace Database\Seeders\Niches\Pest;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Heather Sloan', 'Teravista', 5, 'Ant trail in the kitchen gone after one visit. Quote matched what we paid.', 'Whole-Home Perimeter Treatment', true],
            ['James Porter', 'Forest Creek', 5, 'Quarterly plan keeps spiders and roaches out all year.', 'Quarterly Shield Plan', true],
            ['Maria Santos', 'Behrens Ranch', 5, 'Wasp nest by the patio removed same day — kids can play outside again.', 'Wasp & Nest Removal', true],
            ['Tyler Reed', 'Brushy Creek', 4, 'Rodent exclusion sealed the garage gaps we kept missing.', 'Rodent Exclusion Visit', false],
        ];

        foreach ($testimonials as [$author, $neighborhood, $rating, $text, $tag, $featured]) {
            Testimonial::query()->updateOrCreate(
                ['author' => $author, 'neighborhood' => $neighborhood],
                [
                    'rating' => $rating,
                    'review_text' => $text,
                    'service_tag' => $tag,
                    'is_featured' => $featured,
                ],
            );
        }
    }
}
