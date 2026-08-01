<?php

namespace Tests\Feature;

use App\Enums\ServiceCategory;
use App\Models\Service;
use App\Models\Setting;
use App\Support\Niche\NicheResolver;
use App\Support\Niche\Packs\LawnPack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NichePackTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_niche_is_lawn_pack(): void
    {
        NicheResolver::flush();

        $pack = niche();

        $this->assertInstanceOf(LawnPack::class, $pack);
        $this->assertSame('lawn', $pack->id());
        $this->assertSame('LandscapingBusiness', $pack->schemaOrgType());
        $this->assertSame('sqft_neighborhood', $pack->pricingStrategy());
    }

    public function test_lawn_vocabulary_labels_resolve(): void
    {
        NicheResolver::flush();

        $this->assertSame('The Create Suite', niche_label('suite_create'));
        $this->assertSame('The Care Suite', niche_label('suite_care'));
        $this->assertSame('sq ft', niche_label('size_unit'));
        $this->assertSame('Approx. yard size', niche_label('size_field'));
        $this->assertSame('Neighborhood', niche_label('area_field'));
        $this->assertSame('What are we building?', niche_label('estimate_step_1_title'));
        $this->assertSame('Yard size', niche_label('estimate_progress_2'));
        $this->assertSame('The Create Suite', ServiceCategory::Create->getLabel());
        $this->assertSame('The Care Suite', ServiceCategory::Care->getLabel());
    }

    public function test_seo_json_ld_uses_pack_schema_type(): void
    {
        NicheResolver::flush();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('"@type": "LandscapingBusiness"', false);
    }

    public function test_every_registered_pack_ships_a_favicon(): void
    {
        foreach (array_keys(config('niche.packs')) as $id) {
            $this->assertFileExists(
                public_path("images/favicons/{$id}.svg"),
                "Industry pack [{$id}] is missing public/images/favicons/{$id}.svg.",
            );
        }
    }

    public function test_favicon_follows_active_pack_and_yields_to_an_upload(): void
    {
        NicheResolver::flush();

        $this->get('/')->assertSee('/images/favicons/lawn.svg', false);

        Setting::set('active_niche', 'roofing', 'string', 'product');
        NicheResolver::flush();

        $this->get('/')->assertSee('/images/favicons/roofing.svg', false);

        Setting::set('favicon', 'branding/custom-favicon.png', 'string', 'branding');

        $response = $this->get('/');
        $response->assertSee('/storage/branding/custom-favicon.png', false);
        $response->assertDontSee('/images/favicons/roofing.svg', false);
    }

    public function test_all_registered_packs_expose_hub_blurbs(): void
    {
        $cards = \App\Support\Niche\NicheLoader::hubCards();

        $this->assertCount(8, $cards);
        $this->assertSame(
            ['lawn', 'cleaning', 'roofing', 'pressure', 'windows', 'gutters', 'fence', 'pest'],
            array_column($cards, 'id'),
        );
    }

    public function test_fence_and_windows_packs_show_instant_price_at_minimum_size(): void
    {
        $engine = app(\App\Services\EstimatePricingEngine::class);

        foreach ((new \App\Support\Niche\Packs\FencePack)->settingsDefaults() as [$key, $value, $type, $group]) {
            Setting::set($key, $value, $type, $group);
        }

        $fenceService = Service::create([
            'title' => 'Board Replace',
            'slug' => 'board-replace',
            'category' => ServiceCategory::Care,
            'short_description' => 'Probe',
            'long_description' => 'Probe',
            'icon' => 'sparkles',
            'is_active' => true,
            'base_price_multiplier' => 0.85,
            'sort_order' => 0,
        ]);

        $fenceResult = $engine->calculate($fenceService, 25, 'Phillips Creek', 'simple');
        $this->assertFalse($fenceResult['is_custom']);

        \Illuminate\Support\Facades\Cache::flush();

        foreach ((new \App\Support\Niche\Packs\WindowsPack)->settingsDefaults() as [$key, $value, $type, $group]) {
            Setting::set($key, $value, $type, $group);
        }

        $windowService = Service::create([
            'title' => 'Monthly Route',
            'slug' => 'monthly-route',
            'category' => ServiceCategory::Care,
            'short_description' => 'Probe',
            'long_description' => 'Probe',
            'icon' => 'sparkles',
            'is_active' => true,
            'base_price_multiplier' => 0.88,
            'sort_order' => 0,
        ]);

        $windowResult = $engine->calculate($windowService, 100, 'West 7th', 'simple');
        $this->assertFalse($windowResult['is_custom']);
    }
}
