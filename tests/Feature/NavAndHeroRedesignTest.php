<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Support\Niche\NicheResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavAndHeroRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('header_location', 'Dallas, TX', 'string', 'contact');
        Setting::set('header_tagline', 'Premier Landscape Design & Hardscaping', 'string', 'contact');
        Setting::set('primary_phone', '(214) 617-7725', 'string', 'contact');
        Setting::set('site_name', 'Texas Lawn Legends', 'string', 'general');
        Setting::set('hero_trust_rating', '4.9/5 Rating (140+ Dallas Homeowners)', 'string', 'homepage');
        Setting::set('product_part', 3, 'integer', 'product');
    }

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
        $response->assertSee('Call or Text');
        $response->assertSee('4.9/5 Rating (140+ Dallas Homeowners)');
        $response->assertSee('Transform Your Dallas Yard');
    }

    public function test_empty_site_name_does_not_fall_back_to_texas_lawn_legends(): void
    {
        config(['app.name' => 'Local Services']);
        Setting::set('site_name', '', 'string', 'general');
        Setting::set('logo_text', '', 'string', 'branding');
        Setting::set('header_location', '', 'string', 'contact');
        NicheResolver::flush();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Texas Lawn Legends', false);
        $response->assertDontSee('📍 Dallas, TX', false);
    }
}
