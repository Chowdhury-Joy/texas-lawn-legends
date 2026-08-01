<?php

namespace App\Support\Niche\Packs;

use App\Support\Niche\NichePack;
use Database\Seeders\Niches\Roofing\AddonsSeeder;
use Database\Seeders\Niches\Roofing\PagesSeeder;
use Database\Seeders\Niches\Roofing\SampleProjectSeeder;
use Database\Seeders\Niches\Roofing\ServicesSeeder;
use Database\Seeders\Niches\Roofing\TestimonialsSeeder;

final class RoofingPack implements NichePack
{
    public function id(): string
    {
        return 'roofing';
    }

    public function label(): string
    {
        return 'Roofing';
    }

    public function schemaOrgType(): string
    {
        return 'RoofingContractor';
    }

    public function pricingStrategy(): string
    {
        return 'sqft_neighborhood';
    }

    public function labels(): array
    {
        return [
            'suite_create' => 'Install',
            'suite_care' => 'Repair',
            'size_unit' => 'squares',
            'size_field' => 'Approx. roof size',
            'area_field' => 'Service area',
            'job_noun' => 'roof',
            'job_noun_plural' => 'roofs',
            'tagline_fallback' => 'Roof Install & Storm Repair',
            'estimate_progress_1' => 'Scope',
            'estimate_progress_2' => 'Roof size',
            'estimate_progress_3' => 'Details',
            'estimate_progress_4' => 'Your Estimate',
            'estimate_step_1_title' => 'What roof work do you need?',
            'estimate_step_1_body' => 'Pick install or repair and tell us where the property is.',
            'estimate_step_2_title' => 'Roof size & pitch',
            'estimate_step_2_body' => 'Drag to estimate roof squares, then tell us how involved the job looks.',
            'complexity_simple_desc' => 'Low pitch, easy staging, simple rectangle.',
            'complexity_standard_desc' => 'Typical pitch, a few valleys or penetrations.',
            'complexity_complex_desc' => 'Steep pitch, multi-level, or heavy storm damage.',
        ];
    }

    public function hubBlurb(): string
    {
        return 'Summit Roof Co — installs, repairs, and storm work with the same quote funnel.';
    }

    public function settingsDefaults(): array
    {
        return [
            ['site_name', 'Summit Roof Co', 'string', 'general'],
            ['primary_phone', '(210) 555-0198', 'string', 'contact'],
            ['primary_email', 'hello@summitroof.demo', 'string', 'contact'],
            ['business_address', 'San Antonio, TX 78209', 'string', 'contact'],
            ['business_city', 'San Antonio', 'string', 'contact'],
            ['business_region', 'TX', 'string', 'contact'],
            ['header_location', 'San Antonio, TX', 'string', 'contact'],
            ['header_tagline', 'Roof Install & Storm Repair', 'string', 'contact'],
            ['service_areas', ['Alamo Heights', 'Stone Oak', 'Medical Center', 'Southtown', 'Helotes'], 'json', 'general'],
            ['footer_note', 'Serving Greater San Antonio roofs', 'string', 'general'],
            ['footer_copyright', '© 2026 Summit Roof Co. Demo showcase — not a real company.', 'text', 'general'],
            ['price_per_sqft_modifier', '425', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [
                'Alamo Heights' => 1.25,
                'Stone Oak' => 1.15,
                'Medical Center' => 1.05,
                'Southtown' => 1.00,
                'Helotes' => 1.10,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.90,
                'standard' => 1.00,
                'complex' => 1.40,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '10', 'integer', 'pricing'],
            ['estimate_max_sqft', '80', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.30', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '45000', 'decimal', 'pricing'],
            ['estimate_teaser_low_per_unit', '0.85', 'decimal', 'pricing'],
            ['estimate_teaser_high_per_unit', '1.45', 'decimal', 'pricing'],
            ['estimate_teaser_maintain_multiplier', '0.62', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['8:00 AM', '11:00 AM', '2:00 PM'], 'json', 'pricing'],
            ['operations_alert_email', 'ops@summitroof.demo', 'string', 'operations'],
            ['logo_text', 'Summit Roof Co', 'string', 'branding'],
            ['logo_badge', 'EST. 2016 | San Antonio', 'string', 'branding'],
            ['color_primary', '#7c2d12', 'string', 'branding'],
            ['color_primary_light', '#9a3412', 'string', 'branding'],
            ['color_accent', '#fb923c', 'string', 'branding'],
            ['meta_title', 'Summit Roof Co — San Antonio Roofing Install & Repair', 'string', 'seo'],
            ['meta_description', 'New roofs, storm repairs, and inspections across Alamo Heights, Stone Oak, and Greater San Antonio.', 'text', 'seo'],
            ['meta_keywords', 'San Antonio roofing, roof repair, storm damage, new roof install', 'string', 'seo'],
            ['hero_eyebrow', 'Built for Texas weather', 'string', 'homepage'],
            ['hero_heading', 'A stronger roof. A clearer price.', 'string', 'homepage'],
            ['hero_subheading', 'Instant ranges by roof squares and service area — then lock a free site inspection.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Roof Estimate', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_badge', 'Featured Job', 'string', 'homepage'],
            ['hero_media_neighborhood', 'Alamo Heights', 'string', 'homepage'],
            ['hero_media_title', 'Full Tear-Off & Install', 'string', 'homepage'],
            ['hero_media_subtitle', 'Architectural shingles · Ridge vent · Warranty', 'string', 'homepage'],
            ['hero_trust_rating', '4.8/5 Rating (110+ SA Homeowners)', 'string', 'homepage'],
            ['trust_badges', [
                'GAF Certified',
                'Storm Damage Specialists',
                'Licensed & Insured',
                '10-Year Workmanship Warranty',
            ], 'json', 'homepage'],
            ['process_heading', 'From quote to ridge in 3 steps', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Instant Range', 'body' => 'Enter roof squares and area — get a transparent install or repair range fast.'],
                ['number' => '02', 'title' => 'On-Site Inspection', 'body' => 'Lock a free walkthrough so we verify pitch, layers, and storm damage.'],
                ['number' => '03', 'title' => 'Install & Track', 'body' => 'Follow tear-off, decking, and final inspection in your private dashboard.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Install — New roofs & upgrades', 'string', 'homepage'],
            ['care_suite_heading', 'Repair — Storm & maintenance fixes', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'San Antonio homeowners who trusted Summit with their roof.', 'string', 'homepage'],
            ['cta_heading', 'Ready to protect your home?', 'string', 'homepage'],
            ['cta_subheading', 'Get a roofing price range in under two minutes.', 'text', 'homepage'],
            ['cta_button_label', 'Get Roof Estimate', 'string', 'homepage'],
        ];
    }

    public function contentSeeders(): array
    {
        return [
            ServicesSeeder::class,
            TestimonialsSeeder::class,
            AddonsSeeder::class,
            SampleProjectSeeder::class,
            PagesSeeder::class,
        ];
    }
}
