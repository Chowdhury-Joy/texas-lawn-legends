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
        $this->seedServicesPage();
        $this->seedPortfolioPage();
        $this->seedAboutPage();
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
            $this->block('about', [
                'eyebrow' => 'About Us',
                'image' => null,
                'heading' => 'Dallas Landscaping, Systematized.',
                'body' => 'Texas Lawn Legends was built on a simple belief: premium outdoor spaces should be designed with the same rigor as the homes they surround. Since 2019 we have paired horticultural craft with a repeatable, systematized process — so every yard we touch, from Bishop Arts bungalows to Highland Park estates, gets the same obsessively consistent result.',
                'reverse' => false,
                'stats' => [
                    ['value' => '2019', 'label' => 'Established'],
                    ['value' => '6+', 'label' => 'Dallas Hoods'],
                    ['value' => '500+', 'label' => 'Yards Transformed'],
                    ['value' => '100%', 'label' => 'Systematized'],
                ],
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

    private function seedServicesPage(): void
    {
        $blocks = array_merge(
            $this->block('rich_text', [
                'heading' => 'Landscaping & Lawn Care, Systematized.',
                'body' => '<p>From custom landscape design to hands-off property preservation, every Texas Lawn Legends engagement runs on the same transparent, systematized pipeline. Pick the suite that fits your property, get an instant estimate, and watch the work unfold in your private client dashboard.</p>',
            ]),
            $this->block('service_matrix', [
                'create_suite_heading' => 'The Create Suite — Design & Structural',
                'care_suite_heading' => 'The Care Suite — Maintenance & Preservation',
            ]),
            $this->block('image_text_split', [
                'image' => null,
                'heading' => 'One Process, Every Property',
                'body' => 'Whether you are commissioning a full backyard transformation or enrolling in recurring care, you get the same honest pricing, on-time crews, and photo-documented progress. No surprises — just a better yard, on a schedule.',
                'reverse' => false,
            ]),
            $this->block('cta_banner', [
                'heading' => 'Get an Instant, Transparent Estimate',
                'subheading' => 'See your price range in under two minutes — no sales call required.',
                'button_label' => 'Launch Instant Evaluation',
                'button_url' => null,
            ]),
        );

        Page::query()->updateOrCreate(
            ['slug' => 'services'],
            [
                'title' => 'Our Services',
                'is_published' => true,
                'seo_title' => 'Dallas Landscaping & Lawn Care Services — Texas Lawn Legends',
                'seo_description' => 'Explore the Create Suite (design, sod, hardscaping) and Care Suite (maintenance, preservation) from Texas Lawn Legends across Dallas neighborhoods.',
                'blocks' => $blocks,
            ],
        );
    }

    private function seedPortfolioPage(): void
    {
        $blocks = array_merge(
            $this->block('rich_text', [
                'heading' => 'Verified Local Proof',
                'body' => '<p>Real Dallas yards, real before-and-after results. Every project below is tied to a verified neighborhood review — filter by the part of town you call home.</p>',
            ]),
            $this->block('neighborhood_proof', [
                'heading' => 'Transformations Across Dallas',
                'subheading' => 'Filter by neighborhood to see work near you.',
            ]),
            $this->block('cta_banner', [
                'heading' => 'Your Yard Could Be Next',
                'subheading' => 'Book a 30-minute walkthrough and we will map your transformation.',
                'button_label' => 'Launch Instant Evaluation',
                'button_url' => null,
            ]),
        );

        Page::query()->updateOrCreate(
            ['slug' => 'portfolio'],
            [
                'title' => 'Portfolio',
                'is_published' => true,
                'seo_title' => 'Dallas Landscaping Portfolio & Reviews — Texas Lawn Legends',
                'seo_description' => 'Browse verified before-and-after landscaping transformations and neighborhood reviews from Texas Lawn Legends across Dallas.',
                'blocks' => $blocks,
            ],
        );
    }

    private function seedAboutPage(): void
    {
        $blocks = array_merge(
            $this->block('about', [
                'eyebrow' => 'Our Story',
                'image' => null,
                'heading' => 'We Systematize Growth.',
                'body' => 'Texas Lawn Legends started in 2019 with a single conviction: the best outdoor spaces are not accidents. They are the product of disciplined process, honest pricing, and craftspeople who treat your property like their own. We combined old-school Dallas landscaping know-how with a modern, automated estimation and project-tracking engine — so the experience is as polished as the result.',
                'reverse' => false,
                'stats' => [
                    ['value' => '2019', 'label' => 'Established in Dallas'],
                    ['value' => '500+', 'label' => 'Yards Transformed'],
                    ['value' => '6+', 'label' => 'Neighborhoods Served'],
                    ['value' => '4.9★', 'label' => 'Avg. Client Rating'],
                ],
            ]),
            $this->block('rich_text', [
                'heading' => 'How We Work',
                'body' => '<p>Every project flows through the same transparent pipeline. You start with an instant, automated estimate that prices your property by square footage, neighborhood complexity, and the services you choose — no pushy sales call required.</p>
                <p>Once you book a 30-minute walkthrough, our team maps your space and locks a plan. From design and installation through ongoing care, you follow progress in a private client dashboard with milestones and photos — never wondering what happens next.</p>
                <ul>
                    <li><strong>Create Suite</strong> — landscape design, sod, and structural hardscaping.</li>
                    <li><strong>Care Suite</strong> — maintenance, weed mitigation, and property preservation.</li>
                </ul>',
            ]),
            $this->block('image_text_split', [
                'image' => null,
                'heading' => 'Rooted in Dallas Neighborhoods',
                'body' => 'We are a local crew serving Bishop Arts, Kessler Park, Highland Park, University Park, Oak Lawn, and the surrounding Dallas area. We know the soil, the HOA rules, and the look that fits each block — and we show up on time, every time.',
                'reverse' => true,
            ]),
            $this->block('cta_banner', [
                'heading' => 'Ready To Transform Your Yard?',
                'subheading' => 'Get an instant, transparent estimate in under two minutes.',
                'button_label' => 'Launch Instant Evaluation',
                'button_url' => null,
            ]),
        );

        Page::query()->updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'is_published' => true,
                'seo_title' => 'About Texas Lawn Legends — Dallas Landscaping, Systematized',
                'seo_description' => 'Meet the Dallas landscaping team behind Texas Lawn Legends: systematized growth, honest pricing, and craftsmanship across Bishop Arts, Highland Park, and beyond.',
                'blocks' => $blocks,
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function block(string $type, array $data): array
    {
        return [Str::uuid()->toString() => ['type' => $type, 'data' => $data]];
    }
}
