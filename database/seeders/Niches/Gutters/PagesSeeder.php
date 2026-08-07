<?php

namespace Database\Seeders\Niches\Gutters;

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
                'seo_title' => 'Gutter Services — FlowGuard Gutters',
                'seo_description' => 'Gutter installs, clean-outs, and repairs from FlowGuard Gutters in Plano.',
                'blocks' => array_merge(
                    $this->block('rich_text', [
                        'heading' => 'Gutter work, systematized.',
                        'body' => '<p>Install for new systems and replacements. Repair for clean-outs and fixes. Same transparent quote funnel either way.</p>',
                    ]),
                    $this->block('service_matrix', [
                        'create_suite_heading' => 'Install — New systems & replacements',
                        'care_suite_heading' => 'Repair — Clean-outs & fixes',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Get a gutter quote',
                        'subheading' => 'Transparent pricing in under two minutes.',
                        'button_label' => 'Get Your Gutter Quote',
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
                'seo_title' => 'Gutter Reviews — FlowGuard Gutters',
                'seo_description' => 'Plano homeowners share FlowGuard Gutters results.',
                'blocks' => array_merge(
                    $this->block('neighborhood_proof', [
                        'heading' => 'Proof across Plano',
                        'subheading' => 'Filter by service area to see nearby reviews.',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Your home could be next',
                        'subheading' => 'Book an install and track it in your dashboard.',
                        'button_label' => 'Get Your Gutter Quote',
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
                'seo_title' => 'About FlowGuard Gutters',
                'seo_description' => 'Plano gutter installs and repairs with transparent pricing.',
                'blocks' => array_merge(
                    $this->block('about', [
                        'eyebrow' => 'Our Story',
                        'heading' => 'Protected homes. Clear process.',
                        'body' => 'FlowGuard Gutters is a demo showcase brand — built to show how the same product shells a gutter business with Install and Repair suites.',
                        'stats' => [
                            ['value' => '2017', 'label' => 'Established'],
                            ['value' => '5+', 'label' => 'Plano Areas'],
                            ['value' => '4.9★', 'label' => 'Avg. Rating'],
                        ],
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Ready for worry-free gutters?',
                        'button_label' => 'Get Your Gutter Quote',
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
