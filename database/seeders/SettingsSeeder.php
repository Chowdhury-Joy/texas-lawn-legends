<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\Niche\NicheResolver;
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
     * Core product + neutral brand skeleton, then active niche pack defaults.
     *
     * @return array<int, array{0:string,1:mixed,2:string,3:string}>
     */
    protected function settings(): array
    {
        $core = [
            // Product packaging: 1 = Website+CMS, 2 = +Booking, 3 = +Ops
            ['product_part', '3', 'integer', 'product'],

            // Neutral brand / contact skeleton (packs fill demo values)
            ['site_name', null, 'string', 'general'],
            ['primary_phone', null, 'string', 'contact'],
            ['primary_email', null, 'string', 'contact'],
            ['business_address', null, 'string', 'contact'],
            ['business_city', null, 'string', 'contact'],
            ['business_region', null, 'string', 'contact'],
            ['header_location', null, 'string', 'contact'],
            ['header_tagline', null, 'string', 'contact'],
            ['service_areas', [], 'json', 'general'],
            ['footer_note', null, 'string', 'general'],
            ['footer_copyright', null, 'text', 'general'],

            // Pricing placeholders (pack supplies industry defaults)
            ['price_per_sqft_modifier', '0', 'decimal', 'pricing'],
            ['neighborhood_modifiers', [], 'json', 'pricing'],
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

            // Operations
            ['operations_webhook_url', null, 'string', 'operations'],
            ['operations_alert_email', null, 'string', 'operations'],
            ['lead_escalation_minutes', '10', 'integer', 'operations'],

            // Branding shell
            ['logo_image', null, 'string', 'branding'],
            ['logo_text', null, 'string', 'branding'],
            ['logo_badge', null, 'string', 'branding'],
            ['favicon', null, 'string', 'branding'],
            ['color_primary', '#1b4332', 'string', 'branding'],
            ['color_primary_light', '#2d6a4f', 'string', 'branding'],
            ['color_accent', '#facc15', 'string', 'branding'],
            ['color_slate', '#334155', 'string', 'branding'],
            ['theme', 'clean', 'string', 'branding'],

            // SEO shell
            ['meta_title', null, 'string', 'seo'],
            ['meta_description', null, 'text', 'seo'],
            ['meta_keywords', null, 'string', 'seo'],
            ['og_image', null, 'string', 'seo'],
            ['google_analytics_id', null, 'string', 'seo'],
            ['google_tag_manager_id', null, 'string', 'seo'],
            ['robots_index', '1', 'boolean', 'seo'],

            // Homepage shell
            ['hero_eyebrow', null, 'string', 'homepage'],
            ['hero_heading', null, 'string', 'homepage'],
            ['hero_subheading', null, 'text', 'homepage'],
            ['hero_cta_primary_label', 'Get Started', 'string', 'homepage'],
            ['hero_cta_secondary_label', 'Call or Text', 'string', 'homepage'],
            ['hero_media_image', null, 'string', 'homepage'],
            ['hero_media_badge', null, 'string', 'homepage'],
            ['hero_media_neighborhood', null, 'string', 'homepage'],
            ['hero_media_title', null, 'string', 'homepage'],
            ['hero_media_subtitle', null, 'string', 'homepage'],
            ['hero_trust_rating', null, 'string', 'homepage'],
            ['trust_badges', [], 'json', 'homepage'],
            ['process_heading', null, 'string', 'homepage'],
            ['process_steps', [], 'json', 'homepage'],
            ['create_suite_heading', null, 'string', 'homepage'],
            ['care_suite_heading', null, 'string', 'homepage'],
            ['proof_heading', null, 'string', 'homepage'],
            ['proof_subheading', null, 'string', 'homepage'],
            ['cta_heading', null, 'string', 'homepage'],
            ['cta_subheading', null, 'text', 'homepage'],
            ['cta_button_label', null, 'string', 'homepage'],
        ];

        // Pack defaults overwrite matching keys (same updateOrCreate order below).
        $byKey = [];
        foreach ($core as $row) {
            $byKey[$row[0]] = $row;
        }
        foreach (NicheResolver::active()->settingsDefaults() as $row) {
            $byKey[$row[0]] = $row;
        }

        return array_values($byKey);
    }
}
