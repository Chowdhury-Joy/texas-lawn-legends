<?php

namespace App\Support\Niche\Packs;

use App\Support\Niche\NichePack;
use Database\Seeders\Niches\Windows\AddonsSeeder;
use Database\Seeders\Niches\Windows\PagesSeeder;
use Database\Seeders\Niches\Windows\SampleProjectSeeder;
use Database\Seeders\Niches\Windows\ServicesSeeder;
use Database\Seeders\Niches\Windows\TestimonialsSeeder;

final class WindowsPack implements NichePack
{
    public function id(): string
    {
        return 'windows';
    }

    public function label(): string
    {
        return 'Window cleaning';
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
            'size_unit' => 'panes',
            'size_field' => 'Approx. window count',
            'area_field' => 'Service area',
            'job_noun' => 'home',
            'job_noun_plural' => 'homes',
            'tagline_fallback' => 'Residential & Storefront Windows',
            'timeline_heading' => 'Service Timeline',
            'estimate_progress_1' => 'Scope',
            'estimate_progress_2' => 'Panes',
            'estimate_progress_3' => 'Details',
            'estimate_progress_4' => 'Your Estimate',
            'estimate_step_1_title' => 'Which windows need work?',
            'estimate_step_1_body' => 'Choose interior, exterior, or both and tell us where the property is.',
            'estimate_step_2_title' => 'Pane count & access',
            'estimate_step_2_body' => 'Drag to estimate how many panes, then tell us how involved the visit looks.',
            'complexity_simple_desc' => 'Ground-floor panes, easy access.',
            'complexity_standard_desc' => 'Two-story mix, typical screens and tracks.',
            'complexity_complex_desc' => 'Hard-to-reach panes, skylights, or heavy build-up.',
        ];
    }

    public function hubBlurb(): string
    {
        return 'PanePerfect — interior/exterior panes and recurring routes, same quote funnel.';
    }

    public function settingsDefaults(): array
    {
        return [
            ['site_name', 'PanePerfect', 'string', 'general'],
            ['primary_phone', '(817) 555-0177', 'string', 'contact'],
            ['primary_email', 'hello@paneperfect.demo', 'string', 'contact'],
            ['business_address', 'Fort Worth, TX 76107', 'string', 'contact'],
            ['business_city', 'Fort Worth', 'string', 'contact'],
            ['business_region', 'TX', 'string', 'contact'],
            ['header_location', 'Fort Worth, TX', 'string', 'contact'],
            ['header_tagline', 'Residential & Storefront Windows', 'string', 'contact'],
            ['service_areas', ['West 7th', 'TCU', 'Ridglea', 'Fairmount', 'Downtown FW'], 'json', 'general'],
            ['footer_note', 'Serving Fort Worth homes and storefronts', 'string', 'general'],
            ['footer_copyright', '© 2026 PanePerfect. Demo showcase — not a real company.', 'text', 'general'],
            ['price_per_sqft_modifier', '2.75', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [
                'West 7th' => 1.00,
                'TCU' => 1.05,
                'Ridglea' => 1.10,
                'Fairmount' => 1.08,
                'Downtown FW' => 1.15,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.90,
                'standard' => 1.00,
                'complex' => 1.25,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '12', 'integer', 'pricing'],
            ['estimate_max_sqft', '180', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.15', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '500', 'decimal', 'pricing'],
            ['estimate_teaser_low_per_unit', '0.85', 'decimal', 'pricing'],
            ['estimate_teaser_high_per_unit', '1.45', 'decimal', 'pricing'],
            ['estimate_teaser_maintain_multiplier', '0.62', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['9:00 AM', '12:00 PM', '3:00 PM'], 'json', 'pricing'],
            ['operations_alert_email', 'ops@paneperfect.demo', 'string', 'operations'],
            ['logo_text', 'PanePerfect', 'string', 'branding'],
            ['logo_badge', 'EST. 2018 | Fort Worth, TX', 'string', 'branding'],
            ['color_primary', '#1d4ed8', 'string', 'branding'],
            ['color_primary_light', '#1e40af', 'string', 'branding'],
            ['color_accent', '#93c5fd', 'string', 'branding'],
            ['meta_title', 'PanePerfect — Fort Worth Window Cleaning', 'string', 'seo'],
            ['meta_description', 'Interior and exterior window cleaning across West 7th, TCU, and greater Fort Worth.', 'text', 'seo'],
            ['meta_keywords', 'Fort Worth window cleaning, pane cleaning, storefront windows', 'string', 'seo'],
            ['hero_eyebrow', 'Fort Worth windows, crystal clear', 'string', 'homepage'],
            ['hero_heading', 'Window cleaning you can book in minutes.', 'string', 'homepage'],
            ['hero_subheading', 'Count your panes, pick your area, and lock a slot — transparent pricing every time.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Your Window Quote', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_badge', 'Featured Clean', 'string', 'homepage'],
            ['hero_media_neighborhood', 'West 7th', 'string', 'homepage'],
            ['hero_media_title', 'Full Home Window Detail', 'string', 'homepage'],
            ['hero_media_subtitle', 'Interior · Exterior · Tracks · Screens', 'string', 'homepage'],
            ['hero_trust_rating', '4.9/5 Rating (85+ Fort Worth Homes)', 'string', 'homepage'],
            ['trust_badges', [
                'Fully Insured',
                'Streak-Free Guarantee',
                'Screen & Track Detail',
                'Recurring Routes Available',
            ], 'json', 'homepage'],
            ['process_heading', 'Clear windows in 3 steps', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Instant Quote', 'body' => 'Enter pane count and service area — get a price range in under two minutes.'],
                ['number' => '02', 'title' => 'Lock Your Slot', 'body' => 'Pick a cleaning window that fits your schedule.'],
                ['number' => '03', 'title' => 'Streak-Free Finish', 'body' => 'Track the job with photos and milestones in your client dashboard.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Projects — Deep cleans & move-outs', 'string', 'homepage'],
            ['care_suite_heading', 'Maintain — Recurring pane routes', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'Fort Worth homeowners share their PanePerfect results.', 'string', 'homepage'],
            ['cta_heading', 'Ready for clearer views?', 'string', 'homepage'],
            ['cta_subheading', 'Get a verified window quote in under two minutes.', 'text', 'homepage'],
            ['cta_button_label', 'Get Your Window Quote', 'string', 'homepage'],
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
