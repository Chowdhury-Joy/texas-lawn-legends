<?php

namespace App\Support\Niche\Packs;

use App\Support\Niche\NichePack;
use Database\Seeders\Niches\Cleaning\AddonsSeeder;
use Database\Seeders\Niches\Cleaning\PagesSeeder;
use Database\Seeders\Niches\Cleaning\SampleProjectSeeder;
use Database\Seeders\Niches\Cleaning\ServicesSeeder;
use Database\Seeders\Niches\Cleaning\TestimonialsSeeder;

final class CleaningPack implements NichePack
{
    public function id(): string
    {
        return 'cleaning';
    }

    public function label(): string
    {
        return 'Home cleaning';
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
            'size_unit' => 'interior sq ft',
            'size_field' => 'Approx. cleanable area',
            'area_field' => 'Service area',
            'job_noun' => 'home',
            'job_noun_plural' => 'homes',
            'tagline_fallback' => 'Residential & Office Cleaning',
            'estimate_progress_1' => 'Scope',
            'estimate_progress_2' => 'Area',
            'estimate_progress_3' => 'Details',
            'estimate_progress_4' => 'Your Estimate',
            'estimate_step_1_title' => 'What should we clean?',
            'estimate_step_1_body' => 'Pick the cleaning scope and tell us where the property is.',
            'estimate_step_2_title' => 'Cleanable area & access',
            'estimate_step_2_body' => 'Drag to estimate the area, then tell us how involved the visit looks.',
            'complexity_simple_desc' => 'Light tidy, easy access, mostly open floors.',
            'complexity_standard_desc' => 'Typical home, some clutter or pets.',
            'complexity_complex_desc' => 'Heavy build-up, many rooms, or delicate finishes.',
        ];
    }

    public function hubBlurb(): string
    {
        return 'BrightSide Cleaning — deep cleans, recurring plans, same quote funnel.';
    }

    public function settingsDefaults(): array
    {
        return [
            ['site_name', 'BrightSide Cleaning', 'string', 'general'],
            ['primary_phone', '(512) 555-0142', 'string', 'contact'],
            ['primary_email', 'hello@brightsideclean.demo', 'string', 'contact'],
            ['business_address', 'Austin, TX 78702', 'string', 'contact'],
            ['business_city', 'Austin', 'string', 'contact'],
            ['business_region', 'TX', 'string', 'contact'],
            ['header_location', 'Austin, TX', 'string', 'contact'],
            ['header_tagline', 'Residential & Office Cleaning', 'string', 'contact'],
            ['service_areas', ['East Austin', 'South Congress', 'Hyde Park', 'Mueller', 'Zilker'], 'json', 'general'],
            ['footer_note', 'Serving Austin homes and small offices', 'string', 'general'],
            ['footer_copyright', '© 2026 BrightSide Cleaning. Demo showcase — not a real company.', 'text', 'general'],
            ['price_per_sqft_modifier', '0.12', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [
                'East Austin' => 1.00,
                'South Congress' => 1.10,
                'Hyde Park' => 1.15,
                'Mueller' => 1.05,
                'Zilker' => 1.20,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.85,
                'standard' => 1.00,
                'complex' => 1.35,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '500', 'integer', 'pricing'],
            ['estimate_max_sqft', '5000', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.20', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '1500', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['9:00 AM', '12:00 PM', '3:00 PM'], 'json', 'pricing'],
            ['operations_alert_email', 'ops@brightsideclean.demo', 'string', 'operations'],
            ['logo_text', 'BrightSide Cleaning', 'string', 'branding'],
            ['logo_badge', 'EST. 2021 | Austin, TX', 'string', 'branding'],
            ['color_primary', '#0e7490', 'string', 'branding'],
            ['color_primary_light', '#155e75', 'string', 'branding'],
            ['color_accent', '#67e8f9', 'string', 'branding'],
            ['meta_title', 'BrightSide Cleaning — Austin Home & Office Cleaning', 'string', 'seo'],
            ['meta_description', 'Move-in cleans, recurring maintenance, and deep cleans across East Austin, Hyde Park, and beyond.', 'text', 'seo'],
            ['meta_keywords', 'Austin cleaning, deep clean, house cleaning, office cleaning', 'string', 'seo'],
            ['hero_eyebrow', 'Austin homes, spotless results', 'string', 'homepage'],
            ['hero_heading', 'A cleaner home without the chaos.', 'string', 'homepage'],
            ['hero_subheading', 'Book a deep clean or recurring plan in minutes — transparent pricing, vetted crews.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Your Clean Quote', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_badge', 'Featured Clean', 'string', 'homepage'],
            ['hero_media_neighborhood', 'Hyde Park', 'string', 'homepage'],
            ['hero_media_title', 'Move-In Deep Clean', 'string', 'homepage'],
            ['hero_media_subtitle', 'Kitchen · Baths · Floors · Detail', 'string', 'homepage'],
            ['hero_trust_rating', '4.9/5 Rating (90+ Austin Homes)', 'string', 'homepage'],
            ['trust_badges', [
                'Bonded & Insured',
                'Background-Checked Crews',
                'Eco-Friendly Products Available',
                'Same-Week Booking',
            ], 'json', 'homepage'],
            ['process_heading', 'Clean in 3 clear steps', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Instant Quote', 'body' => 'Tell us your area and square footage — get a transparent price range in under two minutes.'],
                ['number' => '02', 'title' => 'Lock Your Slot', 'body' => 'Pick a walkthrough or first-clean window that fits your schedule.'],
                ['number' => '03', 'title' => 'Spotless Delivery', 'body' => 'Track the job in your client dashboard with photos and milestones.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Projects — Deep cleans & special jobs', 'string', 'homepage'],
            ['care_suite_heading', 'Maintain — Recurring plans that stick', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'Real Austin homeowners share their BrightSide experience.', 'string', 'homepage'],
            ['cta_heading', 'Ready for a brighter space?', 'string', 'homepage'],
            ['cta_subheading', 'Get a verified cleaning quote in under two minutes.', 'text', 'homepage'],
            ['cta_button_label', 'Get Your Clean Quote', 'string', 'homepage'],
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
