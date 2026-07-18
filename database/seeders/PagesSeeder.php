<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedHomepage();
        $this->seedPrivacyPage();
    }

    /**
     * Ports the existing `homepage`-group Setting values into ordered blocks,
     * so the migration to the builder is visually lossless.
     */
    private function seedHomepage(): void
    {
        $blocks = array_merge(
            $this->block('hero', [
                'eyebrow' => Setting::get('hero_eyebrow'),
                'heading' => Setting::get('hero_heading'),
                'subheading' => Setting::get('hero_subheading'),
                'cta_primary_label' => Setting::get('hero_cta_primary_label'),
                'cta_secondary_label' => Setting::get('hero_cta_secondary_label'),
                'media_image' => Setting::get('hero_media_image'),
                'media_badge' => Setting::get('hero_media_badge'),
                'media_neighborhood' => Setting::get('hero_media_neighborhood'),
                'media_title' => Setting::get('hero_media_title'),
                'media_subtitle' => Setting::get('hero_media_subtitle'),
            ]),
            $this->block('trust_bar', [
                'badges' => Setting::get('trust_badges', []),
            ]),
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
            $this->block('faq', [
                'heading' => 'Frequently Asked Questions',
                'items' => [
                    [
                        'question' => 'What neighborhoods in Dallas do you serve?',
                        'answer' => 'We currently serve Bishop Arts, Kessler Park, Highland Park, University Park, Oak Lawn, and surrounding Dallas neighborhoods.',
                    ],
                    [
                        'question' => 'How does the automated estimator calculate pricing?',
                        'answer' => 'Our pricing engine uses a base rate modified by your property square footage, neighborhood complexity factor, and specific service selection to give you an accurate cost range.',
                    ],
                    [
                        'question' => 'What is the difference between the Create and Care suites?',
                        'answer' => 'The Create Suite is for custom landscaping design, sod installation, and structural hardscaping. The Care Suite handles comprehensive ongoing lawn maintenance, weed mitigation, and property preservation.',
                    ],
                    [
                        'question' => 'What happens if my project size exceeds your limits?',
                        'answer' => 'If your estimated project cost exceeds $25,000, or your property square footage exceeds 10,000 sq ft, we bypass the automated pricing engine and transition you directly to a custom architect-led design consultation.',
                    ],
                    [
                        'question' => 'Can I reschedule my walkthrough appointment?',
                        'answer' => 'Yes. You will receive an email and SMS confirmation containing a link to manage, reschedule, or cancel your 30-minute property walkthrough at any time.',
                    ],
                    [
                        'question' => 'How do I access my private client dashboard?',
                        'answer' => 'Once our staff sets up your project, you will be sent a unique dashboard link (e.g. /dashboard/{hash}). No username or password is required—your unique URL serves as your secure access point to see progress photos, milestones, and status updates.',
                    ],
                ],
            ]),
            $this->block('cta_banner', [
                'heading' => Setting::get('cta_heading'),
                'subheading' => Setting::get('cta_subheading'),
                'button_label' => Setting::get('cta_button_label'),
                'button_url' => null,
            ]),
        );

        Page::query()->updateOrCreate(
            ['is_home' => true],
            ['title' => 'Home', 'blocks' => $blocks, 'is_published' => true],
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function block(string $type, array $data): array
    {
        return [Str::uuid()->toString() => ['type' => $type, 'data' => $data]];
    }
}
