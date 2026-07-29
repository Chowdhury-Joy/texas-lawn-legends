<?php

namespace Database\Seeders\Niches\Pressure;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['Marcus Webb', 'Heights', 5, 'Driveway looked brand new. Quote matched the final bill and they finished before noon.', 'Driveway & Walkway Wash', true],
            ['Elena Ruiz', 'Montrose', 5, 'Soft-wash on our siding removed years of green streaks without damage.', 'House & Siding Wash', true],
            ['Tom Bradley', 'Memorial', 5, 'Quarterly plan keeps the pool deck safe and clean all summer.', 'Quarterly Home Wash', true],
            ['Sara Kim', 'Midtown', 4, 'Storefront wash before our grand reopening — spotless windows and entry.', 'Monthly Storefront Wash', false],
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
