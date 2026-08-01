<?php

namespace App\Support\Niche\Packs;

use App\Support\Niche\NichePack;
use Database\Seeders\Niches\Fence\AddonsSeeder;
use Database\Seeders\Niches\Fence\PagesSeeder;
use Database\Seeders\Niches\Fence\SampleProjectSeeder;
use Database\Seeders\Niches\Fence\ServicesSeeder;
use Database\Seeders\Niches\Fence\TestimonialsSeeder;

final class FencePack implements NichePack
{
    public function id(): string
    {
        return 'fence';
    }

    public function label(): string
    {
        return 'Fence & deck';
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
            'size_field' => 'Approx. fence length',
            'area_field' => 'Service area',
            'job_noun' => 'property',
            'job_noun_plural' => 'properties',
            'tagline_fallback' => 'Fence Install & Deck Repair',
            'estimate_progress_1' => 'Scope',
            'estimate_progress_2' => 'Length',
            'estimate_progress_3' => 'Details',
            'estimate_progress_4' => 'Your Estimate',
            'estimate_step_1_title' => 'What fence or deck work do you need?',
            'estimate_step_1_body' => 'Pick install or repair and tell us where the property is.',
            'estimate_step_2_title' => 'Fence length & access',
            'estimate_step_2_body' => 'Drag to estimate linear feet, then tell us how involved the site is.',
            'complexity_simple_desc' => 'Straight run, clear ground, easy gate access.',
            'complexity_standard_desc' => 'Typical yard, a few corners or slopes.',
            'complexity_complex_desc' => 'Steep grade, existing dig-outs, or tight side yards.',
        ];
    }

    public function hubBlurb(): string
    {
        return 'TimberLine Fence & Deck — new installs, board swaps, and stain refresh, same quote funnel.';
    }

    public function settingsDefaults(): array
    {
        return [
            ['site_name', 'TimberLine Fence & Deck', 'string', 'general'],
            ['primary_phone', '(469) 555-0199', 'string', 'contact'],
            ['primary_email', 'hello@timberline.demo', 'string', 'contact'],
            ['business_address', 'Frisco, TX 75034', 'string', 'contact'],
            ['business_city', 'Frisco', 'string', 'contact'],
            ['business_region', 'TX', 'string', 'contact'],
            ['header_location', 'Frisco, TX', 'string', 'contact'],
            ['header_tagline', 'Fence Install & Deck Repair', 'string', 'contact'],
            ['service_areas', ['Phillips Creek', 'Starwood', 'Grayhawk', 'Northeast Frisco', 'Little Elm Border'], 'json', 'general'],
            ['footer_note', 'Serving Frisco fences, gates, and backyard decks', 'string', 'general'],
            ['footer_copyright', '© 2026 TimberLine Fence & Deck. Demo showcase — not a real company.', 'text', 'general'],
            ['price_per_sqft_modifier', '38.00', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [
                'Phillips Creek' => 1.00,
                'Starwood' => 1.10,
                'Grayhawk' => 1.08,
                'Northeast Frisco' => 1.05,
                'Little Elm Border' => 1.02,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.88,
                'standard' => 1.00,
                'complex' => 1.28,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '25', 'integer', 'pricing'],
            ['estimate_max_sqft', '350', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.22', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '2500', 'decimal', 'pricing'],
            ['estimate_teaser_low_per_unit', '0.85', 'decimal', 'pricing'],
            ['estimate_teaser_high_per_unit', '1.45', 'decimal', 'pricing'],
            ['estimate_teaser_maintain_multiplier', '0.62', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['8:00 AM', '11:00 AM', '2:00 PM'], 'json', 'pricing'],
            ['operations_alert_email', 'ops@timberline.demo', 'string', 'operations'],
            ['logo_text', 'TimberLine Fence & Deck', 'string', 'branding'],
            ['logo_badge', 'EST. 2016 | Frisco, TX', 'string', 'branding'],
            ['color_primary', '#92400e', 'string', 'branding'],
            ['color_primary_light', '#78350f', 'string', 'branding'],
            ['color_accent', '#fcd34d', 'string', 'branding'],
            ['meta_title', 'TimberLine — Frisco Fence & Deck', 'string', 'seo'],
            ['meta_description', 'Cedar fence installs, gate builds, and deck repairs across Frisco and Starwood.', 'text', 'seo'],
            ['meta_keywords', 'Frisco fence, cedar fence, deck repair, gate install', 'string', 'seo'],
            ['hero_eyebrow', 'Frisco backyards, built to last', 'string', 'homepage'],
            ['hero_heading', 'Fence and deck work with clear pricing.', 'string', 'homepage'],
            ['hero_subheading', 'Measure your run, pick your area, and book an install or repair — no vague ballparks.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Your Fence Quote', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_badge', 'Featured Install', 'string', 'homepage'],
            ['hero_media_neighborhood', 'Starwood', 'string', 'homepage'],
            ['hero_media_title', 'Cedar Privacy Fence', 'string', 'homepage'],
            ['hero_media_subtitle', '6\' Board-on-Board · Gate · Stain', 'string', 'homepage'],
            ['hero_trust_rating', '4.9/5 Rating (110+ Frisco Properties)', 'string', 'homepage'],
            ['trust_badges', [
                'Licensed & Insured',
                'Cedar & Composite Options',
                'HOA-Ready Designs',
                'Photo Updates on Every Job',
            ], 'json', 'homepage'],
            ['process_heading', 'Built right in 3 steps', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Instant Quote', 'body' => 'Enter linear feet and service area — get a transparent price range in under two minutes.'],
                ['number' => '02', 'title' => 'Lock Your Slot', 'body' => 'Pick an install or repair window that fits your schedule.'],
                ['number' => '03', 'title' => 'TimberLine Finish', 'body' => 'Track the job with photos and milestones in your client dashboard.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Install — New fences & gates', 'string', 'homepage'],
            ['care_suite_heading', 'Repair — Board swaps & deck fixes', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'Frisco homeowners share their TimberLine results.', 'string', 'homepage'],
            ['cta_heading', 'Ready for a better backyard?', 'string', 'homepage'],
            ['cta_subheading', 'Get a verified fence quote in under two minutes.', 'text', 'homepage'],
            ['cta_button_label', 'Get Your Fence Quote', 'string', 'homepage'],
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
