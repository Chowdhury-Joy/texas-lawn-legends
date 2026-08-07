<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnfilledElementHidingTest extends TestCase
{
    use RefreshDatabase;

    public function test_eyebrow_text_renders_when_filled_across_blocks(): void
    {
        $page = Page::create([
            'title' => 'Eyebrow Test',
            'slug' => 'eyebrow-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'eyebrow' => 'Dallas Preferred Landscaper',
                        'heading' => 'Custom Hardscaping',
                    ],
                ],
                [
                    'type' => 'cta_banner',
                    'data' => [
                        'eyebrow' => 'Special Summer Offer',
                        'heading' => 'Transform Your Yard',
                        'button_label' => 'Get Quote',
                    ],
                ],
            ],
        ]);

        $response = $this->get('/eyebrow-test');

        $response->assertStatus(200);
        $response->assertSee('Dallas Preferred Landscaper');
        $response->assertSee('Special Summer Offer');
    }

    public function test_unfilled_block_elements_do_not_render_bracketed_placeholders(): void
    {
        $page = Page::create([
            'title' => 'Unfilled Elements Test',
            'slug' => 'unfilled-elements-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'about',
                    'data' => [
                        'heading' => '',
                        'body' => '',
                        'stats' => [
                            ['value' => '', 'label' => ''],
                        ],
                    ],
                ],
                [
                    'type' => 'three_step',
                    'data' => [
                        'heading' => '',
                        'steps' => [
                            ['title' => '', 'body' => ''],
                        ],
                    ],
                ],
                [
                    'type' => 'stat_band',
                    'data' => [
                        'stats' => [
                            ['value' => '', 'label' => ''],
                        ],
                    ],
                ],
                [
                    'type' => 'testimonial_quote',
                    'data' => [
                        'quote' => '',
                        'author' => '',
                    ],
                ],
                [
                    'type' => 'faq',
                    'data' => [
                        'heading' => '',
                        'items' => [
                            ['question' => '', 'answer' => ''],
                        ],
                    ],
                ],
                [
                    'type' => 'rich_text',
                    'data' => [
                        'heading' => '',
                        'body' => '',
                    ],
                ],
                [
                    'type' => 'gallery',
                    'data' => [
                        'images' => [
                            ['image' => null, 'caption' => ''],
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->get('/unfilled-elements-test');

        $response->assertStatus(200);
        $response->assertDontSee('[About Us Heading]');
        $response->assertDontSee('[Tell visitors who you are');
        $response->assertDontSee('[00]');
        $response->assertDontSee('[Label]');
        $response->assertDontSee('[Step Title]');
        $response->assertDontSee('[Step Description]');
        $response->assertDontSee('[A standout client result');
        $response->assertDontSee('[Client Name]');
        $response->assertDontSee('[Frequently Asked Questions]');
        $response->assertDontSee('[FAQ Question]');
        $response->assertDontSee('[FAQ Answer]');
        $response->assertDontSee('[Rich Text Heading]');
        $response->assertDontSee('[Write rich text contents here]');
        $response->assertDontSee('[Gallery Caption]');
    }
}
