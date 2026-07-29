<?php

namespace App\Support\Niche\Packs;

use App\Support\Niche\NichePack;
use Database\Seeders\Niches\Pressure\AddonsSeeder;
use Database\Seeders\Niches\Pressure\PagesSeeder;
use Database\Seeders\Niches\Pressure\SampleProjectSeeder;
use Database\Seeders\Niches\Pressure\ServicesSeeder;
use Database\Seeders\Niches\Pressure\TestimonialsSeeder;

final class PressurePack implements NichePack
{
    public function id(): string
    {
        return 'pressure';
    }

    public function label(): string
    {
        return 'Pressure washing';
    }

    public function schemaOrgType(): string
    {
        return 'HomeAndConstructionBusiness';
    }

    public function pricingStrategy(): string
    {
        return 'sqft_neighborhood';
    }

    public function labels(): array
    {
        return [
            'suite_create' => 'Projects',
            'suite_care' => 'Maintain',
            'size_unit' => 'sq ft',
            'size_field' => 'Approx. surface area',
            'area_field' => 'Service area',
            'job_noun' => 'property',
            'job_noun_plural' => 'properties',
            'tagline_fallback' => 'Residential & Commercial Wash',
        ];
    }

    public function hubBlurb(): string
    {
        return 'ClearPath Wash — driveways, siding, and storefronts with the same quote funnel.';
    }

    public function settingsDefaults(): array
    {
        return [
            ['site_name', 'ClearPath Wash', 'string', 'general'],
            ['primary_phone', '(713) 555-0164', 'string', 'contact'],
            ['primary_email', 'hello@clearpathwash.demo', 'string', 'contact'],
            ['business_address', 'Houston, TX 77006', 'string', 'contact'],
            ['business_city', 'Houston', 'string', 'contact'],
            ['business_region', 'TX', 'string', 'contact'],
            ['header_location', 'Houston, TX', 'string', 'contact'],
            ['header_tagline', 'Residential & Commercial Wash', 'string', 'contact'],
            ['service_areas', ['Montrose', 'Heights', 'River Oaks', 'Memorial', 'Midtown'], 'json', 'general'],
            ['footer_note', 'Serving Houston homes, driveways, and storefronts', 'string', 'general'],
            ['footer_copyright', '© 2026 ClearPath Wash. Demo showcase — not a real company.', 'text', 'general'],
            ['price_per_sqft_modifier', '0.08', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [
                'Montrose' => 1.00,
                'Heights' => 1.05,
                'River Oaks' => 1.20,
                'Memorial' => 1.15,
                'Midtown' => 1.10,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.85,
                'standard' => 1.00,
                'complex' => 1.30,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '200', 'integer', 'pricing'],
            ['estimate_max_sqft', '8000', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.18', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '2500', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['8:00 AM', '11:00 AM', '2:00 PM'], 'json', 'pricing'],
            ['operations_alert_email', 'ops@clearpathwash.demo', 'string', 'operations'],
            ['logo_text', 'ClearPath Wash', 'string', 'branding'],
            ['logo_badge', 'EST. 2019 | Houston, TX', 'string', 'branding'],
            ['color_primary', '#0369a1', 'string', 'branding'],
            ['color_primary_light', '#075985', 'string', 'branding'],
            ['color_accent', '#7dd3fc', 'string', 'branding'],
            ['meta_title', 'ClearPath Wash — Houston Pressure Washing', 'string', 'seo'],
            ['meta_description', 'Driveway, siding, and patio washes across Montrose, the Heights, and greater Houston.', 'text', 'seo'],
            ['meta_keywords', 'Houston pressure washing, driveway clean, house wash, commercial wash', 'string', 'seo'],
            ['hero_eyebrow', 'Houston surfaces, restored fast', 'string', 'homepage'],
            ['hero_heading', 'Pressure washing without the guesswork.', 'string', 'homepage'],
            ['hero_subheading', 'Get a transparent quote for driveways, siding, and patios — book your slot in minutes.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Your Wash Quote', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_badge', 'Featured Wash', 'string', 'homepage'],
            ['hero_media_neighborhood', 'Heights', 'string', 'homepage'],
            ['hero_media_title', 'Driveway & Siding Wash', 'string', 'homepage'],
            ['hero_media_subtitle', 'Concrete · Brick · Vinyl · Patio', 'string', 'homepage'],
            ['hero_trust_rating', '4.9/5 Rating (120+ Houston Properties)', 'string', 'homepage'],
            ['trust_badges', [
                'Licensed & Insured',
                'Soft-Wash Safe for Siding',
                'Same-Week Scheduling',
                'Before/After Photos',
            ], 'json', 'homepage'],
            ['process_heading', 'Clean surfaces in 3 steps', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Instant Quote', 'body' => 'Enter your service area and surface size — see a clear price range in under two minutes.'],
                ['number' => '02', 'title' => 'Lock Your Slot', 'body' => 'Pick a wash window that fits your schedule.'],
                ['number' => '03', 'title' => 'Restored Finish', 'body' => 'Track the job with photos and milestones in your client dashboard.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Projects — One-time washes & restorations', 'string', 'homepage'],
            ['care_suite_heading', 'Maintain — Recurring wash plans', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'Houston homeowners and shop owners share their ClearPath results.', 'string', 'homepage'],
            ['cta_heading', 'Ready for a cleaner property?', 'string', 'homepage'],
            ['cta_subheading', 'Get a verified wash quote in under two minutes.', 'text', 'homepage'],
            ['cta_button_label', 'Get Your Wash Quote', 'string', 'homepage'],
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
