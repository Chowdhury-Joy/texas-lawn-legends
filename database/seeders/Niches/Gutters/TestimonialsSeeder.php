<?php

namespace Database\Seeders\Niches\Gutters;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Karen Ellis', 'Legacy West', 5, 'Full gutter replacement in one day. Crew left the yard cleaner than they found it.', 'Seamless Gutter Replacement', true],
            ['David Cho', 'West Plano', 5, 'Seasonal clean-out stopped the overflow we had every storm.', 'Seasonal Gutter Clean-Out', true],
            ['Amanda Frost', 'Willow Bend', 5, 'Guard install was worth it — no more ladder weekends.', 'Gutter Guard Install', true],
            ['Mike Torres', 'Downtown Plano', 4, 'Downspout redirect fixed our foundation pooling issue.', 'Downspout Redirect', false],
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
