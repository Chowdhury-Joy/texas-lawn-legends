<?php

namespace App\Support\Niche\Packs;

use App\Support\Niche\NichePack;
use Database\Seeders\Niches\Pest\AddonsSeeder;
use Database\Seeders\Niches\Pest\PagesSeeder;
use Database\Seeders\Niches\Pest\SampleProjectSeeder;
use Database\Seeders\Niches\Pest\ServicesSeeder;
use Database\Seeders\Niches\Pest\TestimonialsSeeder;

final class PestPack implements NichePack
{
    public function id(): string
    {
        return 'pest';
    }

    public function label(): string
    {
        return 'Pest control';
    }

    public function schemaOrgType(): string
    {
        return 'PestControlService';
    }

    public function pricingStrategy(): string
    {
        return 'sqft_neighborhood';
    }

    public function labels(): array
    {
        return [
            'suite_create' => 'Treatments',
            'suite_care' => 'Protect',
            'size_unit' => 'home sq ft',
            'size_field' => 'Approx. home size',
            'area_field' => 'Service area',
            'job_noun' => 'home',
            'job_noun_plural' => 'homes',
            'tagline_fallback' => 'Residential Pest Control',
            'estimate_progress_1' => 'Scope',
            'estimate_progress_2' => 'Home size',
            'estimate_progress_3' => 'Details',
            'estimate_progress_4' => 'Your Estimate',
            'estimate_step_1_title' => 'What needs treatment?',
            'estimate_step_1_body' => 'Choose a one-time treatment or protection plan and tell us where you live.',
            'estimate_step_2_title' => 'Home size & access',
            'estimate_step_2_body' => 'Drag to set your home size, then tell us how involved the job looks.',
            'complexity_simple_desc' => 'Single-story, easy access, light activity.',
            'complexity_standard_desc' => 'Typical home, some cluttered zones or crawl space.',
            'complexity_complex_desc' => 'Multi-story, heavy infestation, or hard-to-reach entry points.',
        ];
    }

    public function hubBlurb(): string
    {
        return 'ShieldBug Pest — one-time treatments and quarterly plans, same quote funnel.';
    }

    public function settingsDefaults(): array
    {
        return [
            ['site_name', 'ShieldBug Pest', 'string', 'general'],
            ['primary_phone', '(512) 555-0155', 'string', 'contact'],
            ['primary_email', 'hello@shieldbug.demo', 'string', 'contact'],
            ['business_address', 'Round Rock, TX 78664', 'string', 'contact'],
            ['business_city', 'Round Rock', 'string', 'contact'],
            ['business_region', 'TX', 'string', 'contact'],
            ['header_location', 'Round Rock, TX', 'string', 'contact'],
            ['header_tagline', 'Residential Pest Control', 'string', 'contact'],
            ['service_areas', ['Teravista', 'Forest Creek', 'Behrens Ranch', 'Downtown RR', 'Brushy Creek'], 'json', 'general'],
            ['footer_note', 'Serving Round Rock homes and townhomes', 'string', 'general'],
            ['footer_copyright', '© 2026 ShieldBug Pest. Demo showcase — not a real company.', 'text', 'general'],
            ['price_per_sqft_modifier', '0.05', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [
                'Teravista' => 1.00,
                'Forest Creek' => 1.05,
                'Behrens Ranch' => 1.08,
                'Downtown RR' => 1.02,
                'Brushy Creek' => 1.06,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.90,
                'standard' => 1.00,
                'complex' => 1.25,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '800', 'integer', 'pricing'],
            ['estimate_max_sqft', '6000', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.18', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '3500', 'decimal', 'pricing'],
            ['estimate_teaser_low_per_unit', '0.85', 'decimal', 'pricing'],
            ['estimate_teaser_high_per_unit', '1.45', 'decimal', 'pricing'],
            ['estimate_teaser_maintain_multiplier', '0.62', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['9:00 AM', '12:00 PM', '3:00 PM'], 'json', 'pricing'],
            ['operations_alert_email', 'ops@shieldbug.demo', 'string', 'operations'],
            ['logo_text', 'ShieldBug Pest', 'string', 'branding'],
            ['logo_badge', 'EST. 2015 | Round Rock, TX', 'string', 'branding'],
            ['color_primary', '#7c3aed', 'string', 'branding'],
            ['color_primary_light', '#6d28d9', 'string', 'branding'],
            ['color_accent', '#c4b5fd', 'string', 'branding'],
            ['meta_title', 'ShieldBug Pest — Round Rock Pest Control', 'string', 'seo'],
            ['meta_description', 'Ant, roach, and rodent treatments plus quarterly plans across Round Rock.', 'text', 'seo'],
            ['meta_keywords', 'Round Rock pest control, ant treatment, quarterly pest plan', 'string', 'seo'],
            ['hero_eyebrow', 'Round Rock homes, pest-free', 'string', 'homepage'],
            ['hero_heading', 'Pest control you can book online.', 'string', 'homepage'],
            ['hero_subheading', 'Get a transparent quote for one-time treatments or quarterly protection — schedule in minutes.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Your Pest Quote', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_badge', 'Featured Treatment', 'string', 'homepage'],
            ['hero_media_neighborhood', 'Teravista', 'string', 'homepage'],
            ['hero_media_title', 'Whole-Home Perimeter Treatment', 'string', 'homepage'],
            ['hero_media_subtitle', 'Interior · Perimeter · Entry Points', 'string', 'homepage'],
            ['hero_trust_rating', '4.9/5 Rating (130+ Round Rock Homes)', 'string', 'homepage'],
            ['trust_badges', [
                'Licensed & Insured',
                'Pet-Safe Options Available',
                'Free Re-Treat Guarantee',
                'Quarterly Plans Available',
            ], 'json', 'homepage'],
            ['process_heading', 'Protected home in 3 steps', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Instant Quote', 'body' => 'Enter home size and service area — get a clear price range in under two minutes.'],
                ['number' => '02', 'title' => 'Lock Your Slot', 'body' => 'Pick a treatment window that fits your schedule.'],
                ['number' => '03', 'title' => 'ShieldBug Finish', 'body' => 'Track the visit with photos and milestones in your client dashboard.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Treatments — One-time knockdowns', 'string', 'homepage'],
            ['care_suite_heading', 'Protect — Quarterly prevention plans', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'Round Rock homeowners share their ShieldBug experience.', 'string', 'homepage'],
            ['cta_heading', 'Ready for a pest-free home?', 'string', 'homepage'],
            ['cta_subheading', 'Get a verified pest quote in under two minutes.', 'text', 'homepage'],
            ['cta_button_label', 'Get Your Pest Quote', 'string', 'homepage'],
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
