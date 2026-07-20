<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Page;
use App\Models\User;
use App\Support\AccessPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUxEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_advanced_layout_permission_registered(): void
    {
        $all = AccessPermissions::all();

        $this->assertArrayHasKey('settings.advanced_layout', $all);
        $this->assertEquals('Advanced Block Layout & Grid Controls', $all['settings.advanced_layout']['label']);
    }

    /**
     * The Create and Edit screens for a Page render the exact same custom
     * Blade view, so the save bar and content width can't drift between
     * "new page" and "existing page" the way they would if only one of the
     * two had a hand-styled sticky bar.
     */
    public function test_create_and_edit_page_screens_render_matching_sticky_save_bars(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $createHtml = $this->actingAs($admin)->get('/admin/pages/create')->assertStatus(200)->getContent();

        $page = Page::create(['title' => 'Parity Check', 'slug' => 'parity-check', 'is_published' => true, 'blocks' => []]);
        $editHtml = $this->actingAs($admin)->get("/admin/pages/{$page->slug}/edit")->assertStatus(200)->getContent();

        foreach ([$createHtml, $editHtml] as $html) {
            $this->assertStringContainsString('sticky bottom-0 z-40', $html);
            $this->assertStringContainsString('Page Content Editor', $html);
            $this->assertStringContainsString('max-w-5xl pb-16', $html);
        }
    }

    public function test_homepage_builder_page_loads(): void
    {
        $user = User::factory()->create(['role' => UserRole::Admin->value]);

        $response = $this->actingAs($user)->get(route('filament.admin.pages.manage-homepage'));
        $response->assertStatus(200);
    }
}
