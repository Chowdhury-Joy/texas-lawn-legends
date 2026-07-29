<?php

namespace App\Support\Niche\Packs;

use App\Support\Niche\NichePack;
use Database\Seeders\Niches\Gutters\AddonsSeeder;
use Database\Seeders\Niches\Gutters\PagesSeeder;
use Database\Seeders\Niches\Gutters\SampleProjectSeeder;
use Database\Seeders\Niches\Gutters\ServicesSeeder;
use Database\Seeders\Niches\Gutters\TestimonialsSeeder;

final class GuttersPack implements NichePack
{
    public function id(): string
    {
        return 'gutters';
    }

    public function label(): string
    {
        return 'Gutter & exterior';
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
            'suite_create' => 'Install',
            'suite_care' => 'Repair',
            'size_unit' => 'linear ft',
            'size_field' => 'Approx. gutter length',
            'area_field' => 'Service area',
            'job_noun' => 'home',
            'job_noun_plural' => 'homes',
            'tagline_fallback' => 'Gutter Install & Seasonal Care',
        ];
    }

    public function hubBlurb(): string
    {
        return 'FlowGuard Gutters — installs, clean-outs, and repairs with the same quote funnel.';
    }

    public function settingsDefaults(): array
    {
        return [
            ['site_name', 'FlowGuard Gutters', 'string', 'general'],
            ['primary_phone', '(972) 555-0188', 'string', 'contact'],
            ['primary_email', 'hello@flowguard.demo', 'string', 'contact'],
            ['business_address', 'Plano, TX 75024', 'string', 'contact'],
            ['business_city', 'Plano', 'string', 'contact'],
            ['business_region', 'TX', 'string', 'contact'],
            ['header_location', 'Plano, TX', 'string', 'contact'],
            ['header_tagline', 'Gutter Install & Seasonal Care', 'string', 'contact'],
            ['service_areas', ['West Plano', 'Legacy West', 'Willow Bend', 'Downtown Plano', 'Frisco Border'], 'json', 'general'],
            ['footer_note', 'Serving Plano homes and townhomes', 'string', 'general'],
            ['footer_copyright', '© 2026 FlowGuard Gutters. Demo showcase — not a real company.', 'text', 'general'],
            ['price_per_sqft_modifier', '4.25', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [
                'West Plano' => 1.00,
                'Legacy West' => 1.12,
                'Willow Bend' => 1.15,
                'Downtown Plano' => 1.05,
                'Frisco Border' => 1.08,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.90,
                'standard' => 1.00,
                'complex' => 1.30,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '60', 'integer', 'pricing'],
            ['estimate_max_sqft', '450', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.20', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '280', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['8:00 AM', '11:00 AM', '2:00 PM'], 'json', 'pricing'],
            ['operations_alert_email', 'ops@flowguard.demo', 'string', 'operations'],
            ['logo_text', 'FlowGuard Gutters', 'string', 'branding'],
            ['logo_badge', 'EST. 2017 | Plano, TX', 'string', 'branding'],
            ['color_primary', '#15803d', 'string', 'branding'],
            ['color_primary_light', '#166534', 'string', 'branding'],
            ['color_accent', '#86efac', 'string', 'branding'],
            ['meta_title', 'FlowGuard Gutters — Plano Gutter Install & Repair', 'string', 'seo'],
            ['meta_description', 'Seamless gutter installs, clean-outs, and repairs across Plano and Legacy West.', 'text', 'seo'],
            ['meta_keywords', 'Plano gutters, gutter install, gutter cleaning, downspout repair', 'string', 'seo'],
            ['hero_eyebrow', 'Plano gutters, protected year-round', 'string', 'homepage'],
            ['hero_heading', 'Gutter work without the runaround.', 'string', 'homepage'],
            ['hero_subheading', 'Measure your run length, pick your area, and book an install or clean-out — clear pricing upfront.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Your Gutter Quote', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_badge', 'Featured Install', 'string', 'homepage'],
            ['hero_media_neighborhood', 'Legacy West', 'string', 'homepage'],
            ['hero_media_title', 'Seamless Gutter Replacement', 'string', 'homepage'],
            ['hero_media_subtitle', '6" K-Style · Downspouts · Guards', 'string', 'homepage'],
            ['hero_trust_rating', '4.9/5 Rating (95+ Plano Homes)', 'string', 'homepage'],
            ['trust_badges', [
                'Licensed & Insured',
                'Seamless On-Site Fabrication',
                'Leaf-Guard Options',
                'Same-Week Estimates',
            ], 'json', 'homepage'],
            ['process_heading', 'Protected gutters in 3 steps', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Instant Quote', 'body' => 'Enter linear feet and service area — get a transparent price range in under two minutes.'],
                ['number' => '02', 'title' => 'Lock Your Slot', 'body' => 'Pick an install or service window that fits your schedule.'],
                ['number' => '03', 'title' => 'FlowGuard Finish', 'body' => 'Track the job with photos and milestones in your client dashboard.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Install — New systems & replacements', 'string', 'homepage'],
            ['care_suite_heading', 'Repair — Clean-outs & fixes', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'Plano homeowners share their FlowGuard experience.', 'string', 'homepage'],
            ['cta_heading', 'Ready for worry-free gutters?', 'string', 'homepage'],
            ['cta_subheading', 'Get a verified gutter quote in under two minutes.', 'text', 'homepage'],
            ['cta_button_label', 'Get Your Gutter Quote', 'string', 'homepage'],
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
