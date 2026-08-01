<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CtaPopupModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_renders_global_estimate_popup_modal_and_dispatch_triggers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('x-data="{ estimateModalOpen: false, modalLocation: \'\' }"', false);
        $response->assertSee('Property Estimate Preview');
        $response->assertSee('Lock In Free Site Visit →');
        $response->assertSee('$dispatch(\'open-estimate-modal\')', false);
        // Projects vs Maintain must change the teaser math (not just button styles).
        $response->assertSee("serviceScope === 'maintenance' ? 0.62 : 1", false);
        $response->assertSee('this.sqft * 0.85 * this.scopeRate()', false);
    }

    public function test_homepage_teaser_rates_come_from_admin_pricing_settings(): void
    {
        Setting::set('estimate_teaser_low_per_unit', 1.10, 'decimal', 'pricing');
        Setting::set('estimate_teaser_high_per_unit', 2.20, 'decimal', 'pricing');
        Setting::set('estimate_teaser_maintain_multiplier', 0.50, 'decimal', 'pricing');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee("serviceScope === 'maintenance' ? 0.5 : 1", false);
        $response->assertSee('this.sqft * 1.1 * this.scopeRate()', false);
        $response->assertSee('this.sqft * 2.2 * this.scopeRate()', false);
    }

    public function test_hero_and_cta_banner_blocks_render_modal_dispatch_triggers(): void
    {
        $page = Page::create([
            'title' => 'Modal Test',
            'slug' => 'modal-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => ['heading' => 'Hero Heading'],
                ],
                [
                    'type' => 'cta_banner',
                    'data' => ['heading' => 'CTA Heading'],
                ],
            ],
        ]);

        $response = $this->get('/modal-test');

        $response->assertStatus(200);
        $response->assertSee('$dispatch(\'open-estimate-modal\', { location: location.trim() })', false);
        $response->assertSee('$dispatch(\'open-estimate-modal\')', false);
    }

    public function test_unfilled_buttons_are_hidden_on_client_site(): void
    {
        $page = Page::create([
            'title' => 'Unfilled Button Test',
            'slug' => 'unfilled-button-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'cta_banner',
                    'data' => [
                        'heading' => 'CTA Without Button',
                        'button_label' => '',
                    ],
                ],
                [
                    'type' => 'hero',
                    'data' => [
                        'heading' => 'Hero Without Secondary CTA',
                        'cta_secondary_label' => '',
                    ],
                ],
            ],
        ]);

        $response = $this->get('/unfilled-button-test');

        $response->assertStatus(200);
        $response->assertDontSee('data-field="button_label"', false);
        $response->assertDontSee('data-field="cta_secondary_label"', false);
    }
}
