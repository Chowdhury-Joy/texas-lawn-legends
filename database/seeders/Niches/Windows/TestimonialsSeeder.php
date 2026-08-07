<?php

namespace Database\Seeders\Niches\Windows;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Rachel Dunn', 'West 7th', 5, 'Every pane inside and out — tracks were spotless. Quote was exactly what we expected.', 'Move-Out Window Detail', true],
            ['Greg Holloway', 'TCU', 5, 'Monthly route keeps our two-story home manageable without ladder weekends.', 'Monthly Home Route', true],
            ['Nina Patel', 'Fairmount', 5, 'Hard-water stains on the shower glass are finally gone.', 'Hard-Water Stain Removal', true],
            ['Leo Martinez', 'Downtown FW', 4, 'Storefront route before weekend events — always on time.', 'Storefront Window Route', false],
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
