<?php

namespace Database\Seeders\Niches\Cleaning;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Maya Ortiz', 'Hyde Park', 5, 'Move-in deep clean was flawless. Quote matched the final bill and the crew left the place sparkling.', 'Move-In Deep Clean', true],
            ['Jordan Blake', 'East Austin', 5, 'We switched to bi-weekly and never look back. Always on time, always thorough.', 'Bi-Weekly Recurring Clean', true],
            ['Priya Shah', 'Zilker', 5, 'Post-construction clean saved us a weekend. Dust was gone from every sill.', 'Post-Construction Clean', true],
            ['Chris Nguyen', 'Mueller', 4, 'Office refresh after hours keeps our studio client-ready without disrupting work.', 'Office Refresh', false],
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
