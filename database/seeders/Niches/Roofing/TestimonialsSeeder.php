<?php

namespace Database\Seeders\Niches\Roofing;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Elena Vargas', 'Alamo Heights', 5, 'Full tear-off finished on schedule. The estimate by squares was honest and the dashboard photos were great for insurance.', 'Full Tear-Off & Install', true],
            ['Marcus Webb', 'Stone Oak', 5, 'Hail hit hard — Summit repaired fast and walked us through every claim photo.', 'Storm Damage Repair', true],
            ['Nina Patel', 'Helotes', 5, 'Leak found and patched the same week. No upsell pressure, just clear options.', 'Leak Diagnosis & Patch', true],
            ['Tom Reyes', 'Southtown', 4, 'Metal install looks sharp and stays cooler upstairs. Crew left the yard clean.', 'Metal Roof Install', false],
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
