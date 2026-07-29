<?php

namespace Database\Seeders\Niches\Pest;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedHomepage();
        $this->seedServicesPage();
        $this->seedPortfolioPage();
        $this->seedAboutPage();
        $this->seedPrivacyPage();
    }

    private function seedHomepage(): void
    {
        $blocks = array_merge(
            $this->block('hero', [
                'eyebrow' => Setting::get('hero_eyebrow'),
                'heading' => Setting::get('hero_heading'),
                'subheading' => Setting::get('hero_subheading'),
                'cta_primary_label' => Setting::get('hero_cta_primary_label'),
                'cta_secondary_label' => Setting::get('hero_cta_secondary_label'),
                'media_badge' => Setting::get('hero_media_badge'),
                'media_neighborhood' => Setting::get('hero_media_neighborhood'),
                'media_title' => Setting::get('hero_media_title'),
                'media_subtitle' => Setting::get('hero_media_subtitle'),
            ]),
            $this->block('trust_bar', ['badges' => Setting::get('trust_badges', [])]),
            $this->block('three_step', [
                'heading' => Setting::get('process_heading'),
                'steps' => Setting::get('process_steps', []),
            ]),
            $this->block('service_matrix', [
                'create_suite_heading' => Setting::get('create_suite_heading'),
                'care_suite_heading' => Setting::get('care_suite_heading'),
            ]),
            $this->block('neighborhood_proof', [
                'heading' => Setting::get('proof_heading'),
                'subheading' => Setting::get('proof_subheading'),
            ]),
            $this->block('cta_banner', [
                'heading' => Setting::get('cta_heading'),
                'subheading' => Setting::get('cta_subheading'),
                'button_label' => Setting::get('cta_button_label'),
            ]),
        );

        Page::query()->updateOrCreate(
            ['is_home' => true],
            ['title' => 'Home', 'blocks' => $blocks, 'is_published' => true],
        );
    }

    private function seedServicesPage(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'services'],
            [
                'title' => 'Our Services',
                'is_published' => true,
                'seo_title' => 'Pest Control Services — ShieldBug Pest',
                'seo_description' => 'One-time treatments and quarterly plans from ShieldBug Pest in Round Rock.',
                'blocks' => array_merge(
                    $this->block('rich_text', [
                        'heading' => 'Pest control, systematized.',
                        'body' => '<p>Treatments for one-time knockdowns. Protect for quarterly prevention. Same transparent quote funnel either way.</p>',
                    ]),
                    $this->block('service_matrix', [
                        'create_suite_heading' => 'Treatments — One-time knockdowns',
                        'care_suite_heading' => 'Protect — Quarterly prevention plans',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Get a pest quote',
                        'subheading' => 'Transparent pricing in under two minutes.',
                        'button_label' => 'Get Your Pest Quote',
                    ]),
                ),
            ],
        );
    }

    private function seedPortfolioPage(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'portfolio'],
            [
                'title' => 'Portfolio',
                'is_published' => true,
                'seo_title' => 'Pest Control Reviews — ShieldBug Pest',
                'seo_description' => 'Round Rock homeowners share ShieldBug Pest results.',
                'blocks' => array_merge(
                    $this->block('neighborhood_proof', [
                        'heading' => 'Proof across Round Rock',
                        'subheading' => 'Filter by service area to see nearby reviews.',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Your home could be next',
                        'subheading' => 'Book a treatment and track it in your dashboard.',
                        'button_label' => 'Get Your Pest Quote',
                    ]),
                ),
            ],
        );
    }

    private function seedAboutPage(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'is_published' => true,
                'seo_title' => 'About ShieldBug Pest',
                'seo_description' => 'Round Rock pest control with transparent pricing and licensed technicians.',
                'blocks' => array_merge(
                    $this->block('about', [
                        'eyebrow' => 'Our Story',
                        'heading' => 'Protected homes. Clear process.',
                        'body' => 'ShieldBug Pest is a demo showcase brand — built to show how the same product shells a pest control business with Treatments and Protect suites.',
                        'stats' => [
                            ['value' => '2015', 'label' => 'Established'],
                            ['value' => '5+', 'label' => 'Round Rock Areas'],
                            ['value' => '4.9★', 'label' => 'Avg. Rating'],
                        ],
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Ready for a pest-free home?',
                        'button_label' => 'Get Your Pest Quote',
                    ]),
                ),
            ],
        );
    }

    private function seedPrivacyPage(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Privacy Compliance Terms',
                'is_published' => true,
                'blocks' => $this->block('rich_text', [
                    'heading' => 'Privacy Compliance Terms',
                    'body' => '<p>Our privacy and compliance documentation will live here.</p>',
                ]),
            ],
        );
    }

    /** @param  array<string, mixed>  $data */
    private function block(string $type, array $data): array
    {
        return [Str::uuid()->toString() => ['type' => $type, 'data' => $data]];
    }
}
