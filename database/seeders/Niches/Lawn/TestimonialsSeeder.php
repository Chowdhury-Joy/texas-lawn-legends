<?php

namespace Database\Seeders\Niches\Lawn;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Marcus Reyes', 'Kessler Park', 5, 'They rebuilt our failing retaining wall and completely transformed the slope in the backyard. Crew was on time every single day and the dashboard let us watch it happen.', 'Retaining Walls', true],
            ['Danielle Cho', 'Bishop Arts', 5, 'The instant estimate tool got us a real number in two minutes, then the site visit matched it almost exactly. No surprises, no upsell games.', 'Custom Patios', true],
            ['Prakash Nair', 'Highland Park', 5, 'Full yard renovation with artificial turf and a flagstone patio. The 3D model they showed us upfront is exactly what we got. Legends is the right name.', 'Artificial Turf Installation', true],
            ['Sofia Alvarez', 'University Park', 4, 'Weekly mowing has been flawless for eight months. Edging is crisp, they always blow off the drive, and billing is painless.', 'Precision Mowing', true],
            ['Trevor Bellinger', 'Oak Lawn', 5, 'Signed up for seasonal cleanup after a storm and they had the whole property cleared in a morning. Booked the maintenance plan on the spot.', 'Seasonal Cleanup Operations', false],
            ['Amara Okafor', 'Kessler Park', 5, 'The mulching and irrigation audit saved our water bill and the beds have never looked better. Genuinely professional operation.', 'Irrigation System Auditing', false],
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
