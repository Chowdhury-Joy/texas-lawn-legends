<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\PageBlocks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsPresetsAndThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_blocks_presets_registry_returns_valid_pre_configured_stacks(): void
    {
        $presets = PageBlocks::presets();

        $this->assertIsArray($presets);
        $this->assertArrayHasKey('landing_page', $presets);
        $this->assertArrayHasKey('portfolio_showcase', $presets);
        $this->assertArrayHasKey('services_suite', $presets);

        foreach ($presets as $key => $preset) {
            $this->assertArrayHasKey('label', $preset);
            $this->assertArrayHasKey('description', $preset);
            $this->assertArrayHasKey('blocks', $preset);
            $this->assertNotEmpty($preset['blocks']);

            foreach ($preset['blocks'] as $block) {
                $this->assertContains($block['type'], PageBlocks::all());
            }
        }
    }

    public function test_admin_homepage_editor_renders_clean_floating_editor_layout(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get('/admin/manage-homepage');

        $response->assertStatus(200);
        $response->assertSee('Homepage Content Editor');
        $response->assertSee('sticky bottom-6 z-30', false);
    }
}
