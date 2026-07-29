<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Support\Niche\NicheLoader;
use App\Support\Niche\NicheResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_hub_lists_industry_cards(): void
    {
        config(['niche.demo_hub' => true]);

        $response = $this->get('/demo');

        $response->assertOk();
        $response->assertSee('Lawn & landscaping');
        $response->assertSee('Home cleaning');
        $response->assertSee('Roofing');
        $response->assertSee('Pressure washing');
        $response->assertSee('Window cleaning');
        $response->assertSee('Gutter & exterior');
        $response->assertSee('Fence & deck');
        $response->assertSee('Pest control');
        $response->assertSee('View live demo');
    }

    public function test_demo_hub_returns_404_when_disabled(): void
    {
        config(['niche.demo_hub' => false]);

        $this->get('/demo')->assertNotFound();
    }

    public function test_loading_cleaning_pack_switches_brand_and_labels(): void
    {
        config(['niche.demo_hub' => true]);

        $this->post('/demo/load', ['niche' => 'cleaning'])
            ->assertRedirect('/');

        NicheResolver::flush();

        $this->assertSame('cleaning', NicheResolver::activeId());
        $this->assertSame('BrightSide Cleaning', setting('site_name'));
        $this->assertSame('Projects', niche_label('suite_create'));
        $this->assertTrue(NicheResolver::demoMode());
        $this->assertTrue(Service::query()->where('title', 'Move-In Deep Clean')->exists());

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('Demo — Home cleaning example');
        $home->assertSee('BrightSide Cleaning');
    }

    public function test_loading_roofing_pack_uses_squares_label(): void
    {
        app(NicheLoader::class)->load('roofing', demoMode: true);
        NicheResolver::flush();

        $this->assertSame('squares', niche_label('size_unit'));
        $this->assertSame('Install', niche_label('suite_create'));
        $this->assertSame('Summit Roof Co', setting('site_name'));
        $this->assertSame('RoofingContractor', niche()->schemaOrgType());
    }

    public function test_loading_windows_pack_uses_panes_label(): void
    {
        app(NicheLoader::class)->load('windows', demoMode: true);
        NicheResolver::flush();

        $this->assertSame('panes', niche_label('size_unit'));
        $this->assertSame('Projects', niche_label('suite_create'));
        $this->assertSame('PanePerfect', setting('site_name'));
        $this->assertTrue(Service::query()->where('title', 'Move-Out Window Detail')->exists());
    }

    public function test_loading_pest_pack_uses_treatments_and_protect_labels(): void
    {
        app(NicheLoader::class)->load('pest', demoMode: true);
        NicheResolver::flush();

        $this->assertSame('Treatments', niche_label('suite_create'));
        $this->assertSame('Protect', niche_label('suite_care'));
        $this->assertSame('ShieldBug Pest', setting('site_name'));
        $this->assertSame('PestControlService', niche()->schemaOrgType());
    }

    public function test_demo_reset_route_resets_current_pack(): void
    {
        config(['niche.demo_hub' => true]);

        app(NicheLoader::class)->load('cleaning', demoMode: true);
        NicheResolver::flush();

        $this->post('/demo/reset')->assertRedirect('/');

        NicheResolver::flush();

        $this->assertSame('cleaning', NicheResolver::activeId());
        $this->assertSame('BrightSide Cleaning', setting('site_name'));

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('Demo — Home cleaning example');
        $home->assertSee('BrightSide Cleaning');
    }

    public function test_admin_industry_packs_page_is_reachable(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@admin.com',
            'role' => \App\Enums\UserRole::Admin,
        ]);

        $this->actingAs($admin)
            ->get('/admin/manage-industry-packs')
            ->assertOk();
    }

    public function test_operations_notifier_skips_webhook_in_demo_mode(): void
    {
        Setting::set('demo_mode', true, 'boolean', 'product');
        Setting::set('operations_webhook_url', 'https://example.test/hooks', 'string', 'operations');
        NicheResolver::flush();

        \Illuminate\Support\Facades\Http::fake();

        app(\App\Services\OperationsNotifier::class)->dispatch('Test', ['ok' => true]);

        \Illuminate\Support\Facades\Http::assertNothingSent();
    }
}
