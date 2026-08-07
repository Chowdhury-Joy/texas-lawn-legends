<?php

namespace Tests\Feature;

use App\Enums\ProductPart;
use App\Enums\UserRole;
use App\Filament\Pages\ManageProductParts;
use App\Models\Setting;
use App\Models\User;
use App\Support\ProductFeatures;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductPartGatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_to_full_ops_part_when_unset(): void
    {
        $this->assertSame(ProductPart::Ops, product_part());
        $this->assertTrue(product_part_at_least(3));
        $this->assertTrue(ProductFeatures::allows('resource.leads'));
        $this->assertTrue(ProductFeatures::allows('resource.projects'));
    }

    public function test_part_one_blocks_booking_and_ops_routes(): void
    {
        Setting::set('product_part', ProductPart::Website->value, 'integer', 'product');

        $this->get('/estimate')->assertNotFound();
        $this->get('/portal')->assertNotFound();
        $this->assertFalse(ProductFeatures::allows('resource.leads'));
        $this->assertFalse(ProductFeatures::allows('settings.pricing'));
        $this->assertFalse(ProductFeatures::allows('resource.projects'));
    }

    public function test_part_two_allows_estimate_but_not_portal(): void
    {
        Setting::set('product_part', ProductPart::Booking->value, 'integer', 'product');

        $this->get('/estimate')->assertOk();
        $this->get('/portal')->assertNotFound();
        $this->assertTrue(ProductFeatures::allows('resource.leads'));
        $this->assertFalse(ProductFeatures::allows('resource.projects'));
    }

    public function test_part_three_allows_estimate_and_portal(): void
    {
        Setting::set('product_part', ProductPart::Ops->value, 'integer', 'product');

        $this->get('/estimate')->assertOk();
        $this->get('/portal')->assertOk();
        $this->assertTrue(ProductFeatures::allows('resource.projects'));
    }

    public function test_admin_can_open_product_parts_settings(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/admin/manage-product-parts')
            ->assertOk();
    }

    public function test_saving_product_part_redirects_so_admin_nav_refreshes(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin);

        Livewire::test(ManageProductParts::class)
            ->fillForm([
                'product_part' => ProductPart::Website->value,
            ])
            ->call('save')
            ->assertRedirect(ManageProductParts::getUrl());

        $this->assertSame(ProductPart::Website, product_part());
        $this->assertFalse(ProductFeatures::allows('resource.leads'));
    }

    public function test_part_one_hides_ops_admin_resources_from_admins(): void
    {
        Setting::set('product_part', ProductPart::Website->value, 'integer', 'product');

        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/admin/leads')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin/projects')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin/pages')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/manage-product-parts')
            ->assertOk();
    }

    public function test_layout_hides_estimate_cta_on_part_one(): void
    {
        Setting::set('product_part', ProductPart::Website->value, 'integer', 'product');
        Setting::set('site_name', 'Demo Site', 'string', 'general');
        Setting::set('logo_text', 'Demo Site', 'string', 'branding');
        Setting::set('primary_phone', '555-0100', 'string', 'contact');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('Get Free Estimate', $html);
        $this->assertStringNotContainsString('Client Portal', $html);
    }
}
