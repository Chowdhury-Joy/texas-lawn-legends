<?php

namespace Database\Seeders\Niches\Windows;

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
                'seo_title' => 'Window Cleaning Services — PanePerfect',
                'seo_description' => 'Interior, exterior, and recurring window routes from PanePerfect in Fort Worth.',
                'blocks' => array_merge(
                    $this->block('rich_text', [
                        'heading' => 'Window cleaning, systematized.',
                        'body' => '<p>Projects for deep cleans and special jobs. Maintain for recurring routes. Same transparent quote funnel either way.</p>',
                    ]),
                    $this->block('service_matrix', [
                        'create_suite_heading' => 'Projects — Deep cleans & move-outs',
                        'care_suite_heading' => 'Maintain — Recurring pane routes',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Get a window quote',
                        'subheading' => 'Transparent pricing in under two minutes.',
                        'button_label' => 'Get Your Window Quote',
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
                'seo_title' => 'Window Cleaning Reviews — PanePerfect',
                'seo_description' => 'Fort Worth homeowners share PanePerfect results.',
                'blocks' => array_merge(
                    $this->block('neighborhood_proof', [
                        'heading' => 'Proof across Fort Worth',
                        'subheading' => 'Filter by service area to see nearby reviews.',
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Your home could be next',
                        'subheading' => 'Book a clean and track it in your dashboard.',
                        'button_label' => 'Get Your Window Quote',
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
                'seo_title' => 'About PanePerfect',
                'seo_description' => 'Fort Worth window cleaning with transparent pricing and insured crews.',
                'blocks' => array_merge(
                    $this->block('about', [
                        'eyebrow' => 'Our Story',
                        'heading' => 'Clear glass. Clear process.',
                        'body' => 'PanePerfect is a demo showcase brand — built to show how the same product shells a window cleaning business with Projects and Maintain suites.',
                        'stats' => [
                            ['value' => '2018', 'label' => 'Established'],
                            ['value' => '5+', 'label' => 'Fort Worth Areas'],
                            ['value' => '4.9★', 'label' => 'Avg. Rating'],
                        ],
                    ]),
                    $this->block('cta_banner', [
                        'heading' => 'Ready for clearer views?',
                        'button_label' => 'Get Your Window Quote',
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
