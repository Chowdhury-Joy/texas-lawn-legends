<?php

namespace Tests\Feature;

use App\Enums\ServiceCategory;
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
        $this->assertSame('Neighborhood', niche_label('area_field'));
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
}
