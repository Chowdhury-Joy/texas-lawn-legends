<?php

namespace Database\Seeders\Niches\Roofing;

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
                'seo_title' => 'Roofing Services — Summit Roof Co',
                'seo_description' => 'Install and repair services from Summit Roof Co in San Antonio.',
                'blocks' => array_merge(
                    $this->block('rich_text', [
                        'heading' => 'Roofs built for Texas weather.',
                        'body' => '<p>Install for new systems and upgrades. Repair for storm and maintenance work. Same transparent squares-based quote funnel.</p>',
                    ]),
                    $this->block('service_matrix', [
                        'create_suite_heading' => 'Install — New roofs & upgrades',
                        'care_suite_heading' => 'Repair — Storm & maintenance',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Get a roofing estimate',
                        'subheading' => 'Price by squares in under two minutes.',
                        'button_label' => 'Get Roof Estimate',
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
                'seo_title' => 'Roofing Projects — Summit Roof Co',
                'seo_description' => 'San Antonio roof installs and repairs from Summit Roof Co.',
                'blocks' => array_merge(
                    $this->block('neighborhood_proof', [
                        'heading' => 'Proof across San Antonio',
                        'subheading' => 'Filter by service area to see nearby work.',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Protect your home next',
                        'button_label' => 'Get Roof Estimate',
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
                'seo_title' => 'About Summit Roof Co',
                'seo_description' => 'San Antonio roofing demo showcase — installs, repairs, clear pricing.',
                'blocks' => array_merge(
                    $this->block('about', [
                        'eyebrow' => 'Our Story',
                        'heading' => 'Stronger roofs. Clearer prices.',
                        'body' => 'Summit Roof Co is a demo showcase brand — proving the same product can shell a roofing company with Install and Repair suites.',
                        'stats' => [
                            ['value' => '2016', 'label' => 'Established'],
                            ['value' => '5+', 'label' => 'SA Areas'],
                            ['value' => '4.8★', 'label' => 'Avg. Rating'],
                        ],
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Ready to protect your home?',
                        'button_label' => 'Get Roof Estimate',
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
