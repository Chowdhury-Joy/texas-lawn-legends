<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavAndHeroRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_info_banner_and_header_navbar_render_cta_button(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('📍 Dallas, TX');
        $response->assertSee('Get Free Estimate');
    }

    public function test_hero_block_renders_unified_location_pricing_form_and_secondary_cta(): void
    {
        $page = Page::create([
            'title' => 'Nav Hero Test',
            'slug' => 'nav-hero-test',
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

        $response = $this->get('/nav-hero-test');

        $response->assertStatus(200);
        $response->assertSee('View Instant Pricing →');
        $response->assertSee('placeholder="Enter Dallas ZIP code or neighborhood..."', false);
        $response->assertSee('Call or Text');
        $response->assertSee('4.9/5 Rating (140+ Dallas Homeowners)');
    }
}
