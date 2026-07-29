<?php

namespace Database\Seeders\Niches\Fence;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Jessica Crane', 'Starwood', 5, '180 feet of cedar privacy fence in four days. Gate hardware is solid and the stain looks great.', 'Cedar Privacy Fence', true],
            ['Robert Hale', 'Phillips Creek', 5, 'Board replacement after the windstorm — matched the existing fence perfectly.', 'Board & Pickets Replacement', true],
            ['Linda Wu', 'Grayhawk', 5, 'Deck rail repair before summer parties. Crew was tidy and on schedule.', 'Deck Board & Rail Repair', true],
            ['Carlos Mendez', 'Northeast Frisco', 4, 'Custom double gate for our RV pad — opens smooth every time.', 'Custom Gate Build', false],
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
