<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCroEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_hero_block_renders_neighborhood_checker_and_social_proof(): void
    {
        $page = Page::create([
            'title' => 'CRO Test',
            'slug' => 'cro-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'heading' => 'Transform Your Dallas Yard',
                    ],
                ],
            ],
        ]);

        $response = $this->get('/cro-test');

        $response->assertStatus(200);
        $response->assertSee('View Instant Property Valuation');
        $response->assertSee('4.9/5 Rating');
    }

    public function test_layout_renders_mobile_sticky_action_rail(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('⚡ 2-Min Price Quote');
        $response->assertSee('lg:hidden', false);
    }

    public function test_cta_banner_and_service_matrix_render_cro_widgets(): void
    {
        $page = Page::create([
            'title' => 'CRO Widgets Test',
            'slug' => 'cro-widgets-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'cta_banner',
                    'data' => ['heading' => 'CTA Heading'],
                ],
                [
                    'type' => 'service_matrix',
                    'data' => [],
                ],
            ],
        ]);

        $response = $this->get('/cro-widgets-test');

        $response->assertStatus(200);
        $response->assertSee('Verified 5-Star Local Service');
        $response->assertSee('Instant Price Range Preview');
    }
}
