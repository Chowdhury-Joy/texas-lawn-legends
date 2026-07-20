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

    /**
     * The critical failure case: a block already has classic-field content, then the editor
     * adds a SINGLE entry to the text_elements repeater. All other classic fields must still
     * appear on the live page — none should be silently dropped.
     */
    public function test_classic_fields_survive_partial_repeater_population(): void
    {
        $page = Page::create([
            'title' => 'Partial Population Test',
            'slug' => 'partial-population-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        // Classic fields — existing real content
                        'heading' => 'CLASSIC HEADING',
                        'subheading' => 'CLASSIC SUBHEADING',
                        'cta_primary_label' => 'CLASSIC PRIMARY CTA',
                        // Editor adds only ONE repeater entry (just the eyebrow):
                        'text_elements' => [
                            ['type' => 'eyebrow', 'text' => 'CLASSIC EYEBROW'],
                        ],
                    ],
                ],
            ],
        ]);

        $html = $this->get('/partial-population-test')->assertStatus(200)->getContent();

        // Eyebrow came from the repeater
        $this->assertStringContainsString('CLASSIC EYEBROW', $html);

        // These must NOT disappear just because text_elements is partially populated
        $this->assertStringContainsString('CLASSIC HEADING', $html, 'heading vanished after partial repeater population');
        $this->assertStringContainsString('CLASSIC SUBHEADING', $html, 'subheading vanished after partial repeater population');
        $this->assertStringContainsString('CLASSIC PRIMARY CTA', $html, 'primary CTA vanished after partial repeater population');
    }

    /**
     * When text_elements is fully populated, items render in the saved drag order,
     * not in the hardcoded default order.
     */
    public function test_draggable_text_elements_render_in_custom_order(): void
    {
        $page = Page::create([
            'title' => 'Custom Order Test',
            'slug' => 'custom-order-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'text_elements' => [
                            ['type' => 'heading', 'text' => 'FIRST HEADLINE'],
                            ['type' => 'eyebrow', 'text' => 'SECOND EYEBROW'],
                        ],
                    ],
                ],
            ],
        ]);

        $html = $this->get('/custom-order-test')->assertStatus(200)->getContent();

        $this->assertStringContainsString('FIRST HEADLINE', $html);
        $this->assertStringContainsString('SECOND EYEBROW', $html);

        // Heading must appear before eyebrow in the HTML output
        $this->assertLessThan(
            strpos($html, 'SECOND EYEBROW'),
            strpos($html, 'FIRST HEADLINE'),
            'text_elements drag order was not respected in rendered output'
        );
    }
}
