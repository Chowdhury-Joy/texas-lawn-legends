<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->settings() as [$key, $value, $type, $group]) {
            $encoded = in_array($type, ['json', 'array'], true) ? json_encode($value) : $value;

            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $encoded, 'type' => $type, 'group' => $group],
            );
        }
    }

    /**
     * @return array<int, array{0:string,1:mixed,2:string,3:string}>
     */
    protected function settings(): array
    {
        return [
            // ---------------- Contact / General ----------------
            ['site_name', 'Texas Lawn Legends', 'string', 'general'],
            ['primary_phone', '(214) 617-7725', 'string', 'contact'],
            ['primary_email', 'hello@texaslawnlegends.com', 'string', 'contact'],
            ['business_address', 'Dallas, TX 75208', 'string', 'contact'],
            ['service_areas', ['Bishop Arts', 'Kessler Park', 'Highland Park', 'University Park', 'Oak Lawn'], 'json', 'general'],
            ['footer_note', 'Serving Dallas & surrounding neighborhoods', 'string', 'general'],
            ['footer_copyright', '© 2023 - 2026 Texas Lawn Legends. All Rights Reserved. Systematized Growth Architecture powered by Laravel.', 'text', 'general'],
            ['price_per_sqft_modifier', '3.25', 'decimal', 'pricing'],

            // ---------------- Estimator / Pricing Engine ----------------
            ['neighborhood_modifiers', [
                'Bishop Arts' => 1.10,
                'Kessler Park' => 1.15,
                'Highland Park' => 1.35,
                'University Park' => 1.30,
                'Oak Lawn' => 1.20,
            ], 'json', 'pricing'],
            ['complexity_modifiers', [
                'simple' => 0.85,
                'standard' => 1.00,
                'complex' => 1.30,
            ], 'json', 'pricing'],
            ['estimate_min_sqft', '100', 'integer', 'pricing'],
            ['estimate_max_sqft', '10000', 'integer', 'pricing'],
            ['estimate_high_multiplier', '1.25', 'decimal', 'pricing'],
            ['estimate_custom_threshold', '25000', 'decimal', 'pricing'],
            ['booking_days_offered', '5', 'integer', 'pricing'],
            ['booking_time_slots', ['9:00 AM', '12:00 PM', '3:00 PM'], 'json', 'pricing'],

            // ---------------- Operations Alerts ----------------
            ['operations_webhook_url', null, 'string', 'operations'],
            ['operations_alert_email', 'ops@texaslawnlegends.com', 'string', 'operations'],
            ['lead_escalation_minutes', '10', 'integer', 'operations'],

            // ---------------- Branding & Theme ----------------
            ['logo_image', null, 'string', 'branding'],
            ['logo_text', 'Texas Lawn Legends', 'string', 'branding'],
            ['logo_badge', 'EST. 2019 | Dallas, TX', 'string', 'branding'],
            ['favicon', null, 'string', 'branding'],
            ['brand_font', 'Montserrat', 'string', 'branding'],
            ['color_primary', '#1b4332', 'string', 'branding'],
            ['color_primary_light', '#2d6a4f', 'string', 'branding'],
            ['color_accent', '#facc15', 'string', 'branding'],
            ['color_slate', '#334155', 'string', 'branding'],
            ['theme', 'clean', 'string', 'branding'], // clean | minimal | editorial | rounded | retro | bold

            // ---------------- SEO ----------------
            ['meta_title', 'Texas Lawn Legends — Premium Dallas Landscaping & Lawn Care', 'string', 'seo'],
            ['meta_description', 'Professional landscape design, precision hardscaping, and premier maintenance serving Bishop Arts, Kessler Park, Highland Park, University Park, and Oak Lawn.', 'text', 'seo'],
            ['meta_keywords', 'Dallas landscaping, hardscaping, retaining walls, lawn maintenance, artificial turf, sod installation', 'string', 'seo'],
            ['og_image', null, 'string', 'seo'],
            ['google_analytics_id', null, 'string', 'seo'],
            ['google_tag_manager_id', null, 'string', 'seo'],
            ['robots_index', '1', 'boolean', 'seo'],

            // ---------------- Homepage Content ----------------
            ['hero_eyebrow', 'Setting the Standard for Dallas Landscaping', 'string', 'homepage'],
            ['hero_heading', 'Transform Your Dallas Yard Into An Outdoor Retreat.', 'string', 'homepage'],
            ['hero_subheading', 'Professional design, precision hardscaping, and premier maintenance you can actually rely on.', 'text', 'homepage'],
            ['hero_cta_primary_label', 'Start Your Free Estimate', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_image', null, 'string', 'homepage'],
            ['hero_media_badge', 'Featured Build', 'string', 'homepage'],
            ['hero_media_neighborhood', 'Kessler Park', 'string', 'homepage'],
            ['hero_media_title', 'Full Yard Renovation', 'string', 'homepage'],
            ['hero_media_subtitle', 'Retaining walls · Flagstone patio · New sod', 'string', 'homepage'],
            ['trust_badges', [
                'As Seen On CBS News',
                'Oak Cliff Chamber of Commerce Member',
                'USPS Registered Contractor',
                'Dallas Local Driving Academy Fleet Partner',
            ], 'json', 'homepage'],
            ['process_heading', 'Our 3-Step Transformation Process — Deliver The Wow', 'string', 'homepage'],
            ['process_steps', [
                ['number' => '01', 'title' => 'Get Your Digital Valuation', 'body' => 'Share your target landscaping goals by filling out our custom multi-step estimator tool. Our automated engine processes parameters immediately to project your specific scope.'],
                ['number' => '02', 'title' => 'Lock In Your Site Visit', 'body' => 'Review your structural price estimation parameters and immediately lock down a verified 30-minute face-to-face property walkthrough via our integrated booking engine. No obligation, no friction.'],
                ['number' => '03', 'title' => 'See Your Landscape Transform', 'body' => 'Relax as our professional crew brings your visualization to life. Track real-time milestone transitions and progress media directly inside your private customer dashboard asset.'],
            ], 'json', 'homepage'],
            ['create_suite_heading', 'Premium Landscape Design & Structural Hardscaping', 'string', 'homepage'],
            ['care_suite_heading', 'Comprehensive Property Preservation & Lawn Maintenance', 'string', 'homepage'],
            ['proof_heading', 'Verified Local Proof', 'string', 'homepage'],
            ['proof_subheading', 'Real transformations and reviews from the Dallas neighborhoods we serve.', 'string', 'homepage'],
            ['cta_heading', 'Ready To Systematize Your Property Transformation?', 'string', 'homepage'],
            ['cta_subheading', 'Stop guessing on project scopes. Spend 2 minutes with our automated qualification calculator to unlock verified local rates today.', 'text', 'homepage'],
            ['cta_button_label', 'Launch Instant Evaluation Engine', 'string', 'homepage'],
        ];
    }
}
