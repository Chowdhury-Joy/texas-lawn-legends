<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Page;
use App\Models\User;
use App\Support\AccessPermissions;
use App\Support\PageBlocks;
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
     *
     * Covers all three affected block types to prevent the "test only the easy block" gap.
     */
    public function test_classic_fields_survive_partial_repeater_population_hero(): void
    {
        Page::create([
            'title' => 'Hero Partial Test',
            'slug' => 'hero-partial-test',
            'is_published' => true,
            'blocks' => [[
                'type' => 'hero',
                'data' => [
                    'heading' => 'HERO HEADING',
                    'subheading' => 'HERO SUBHEADING',
                    'cta_primary_label' => 'HERO PRIMARY CTA',
                    // Only the eyebrow goes into the repeater
                    'text_elements' => [['type' => 'eyebrow', 'text' => 'HERO EYEBROW']],
                ],
            ]],
        ]);

        $html = $this->get('/hero-partial-test')->assertStatus(200)->getContent();

        $this->assertStringContainsString('HERO EYEBROW', $html);
        $this->assertStringContainsString('HERO HEADING', $html, 'hero heading vanished after partial repeater population');
        $this->assertStringContainsString('HERO SUBHEADING', $html, 'hero subheading vanished after partial repeater population');
        $this->assertStringContainsString('HERO PRIMARY CTA', $html, 'hero primary CTA vanished after partial repeater population');
    }

    public function test_classic_fields_survive_partial_repeater_population_cta_banner(): void
    {
        Page::create([
            'title' => 'CTA Partial Test',
            'slug' => 'cta-partial-test',
            'is_published' => true,
            'blocks' => [[
                'type' => 'cta_banner',
                'data' => [
                    'heading' => 'CTA HEADING',
                    'subheading' => 'CTA SUBHEADING',
                    'button_label' => 'CTA BUTTON',
                    // Only the eyebrow goes into the repeater
                    'text_elements' => [['type' => 'eyebrow', 'text' => 'CTA EYEBROW']],
                ],
            ]],
        ]);

        $html = $this->get('/cta-partial-test')->assertStatus(200)->getContent();

        $this->assertStringContainsString('CTA EYEBROW', $html);
        $this->assertStringContainsString('CTA HEADING', $html, 'cta_banner heading vanished after partial repeater population');
        $this->assertStringContainsString('CTA SUBHEADING', $html, 'cta_banner subheading vanished after partial repeater population');
        $this->assertStringContainsString('CTA BUTTON', $html, 'cta_banner button label vanished after partial repeater population');
    }

    public function test_classic_fields_survive_partial_repeater_population_three_step(): void
    {
        Page::create([
            'title' => '3-Step Partial Test',
            'slug' => 'three-step-partial-test',
            'is_published' => true,
            'blocks' => [[
                'type' => 'three_step',
                'data' => [
                    'heading' => 'THREE STEP HEADING',
                    'subheading' => 'THREE STEP SUBHEADING',
                    // Only the eyebrow goes into the repeater
                    'text_elements' => [['type' => 'eyebrow', 'text' => 'THREE STEP EYEBROW']],
                ],
            ]],
        ]);

        $html = $this->get('/three-step-partial-test')->assertStatus(200)->getContent();

        $this->assertStringContainsString('THREE STEP EYEBROW', $html);
        $this->assertStringContainsString('THREE STEP HEADING', $html, 'three_step heading vanished after partial repeater population');
        $this->assertStringContainsString('THREE STEP SUBHEADING', $html, 'three_step subheading vanished after partial repeater population');
    }

    /**
     * When text_elements is fully populated, items render in the saved drag order,
     * not in the hardcoded default order.
     */
    public function test_draggable_text_elements_render_in_custom_order(): void
    {
        Page::create([
            'title' => 'Custom Order Test',
            'slug' => 'custom-order-test',
            'is_published' => true,
            'blocks' => [[
                'type' => 'hero',
                'data' => [
                    'text_elements' => [
                        ['type' => 'heading', 'text' => 'FIRST HEADLINE'],
                        ['type' => 'eyebrow', 'text' => 'SECOND EYEBROW'],
                    ],
                ],
            ]],
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

    // ─── Unit tests for PageBlocks::seedTextElementsForBlock() ───────────────

    public function test_seed_hero_populates_text_elements_from_classic_fields(): void
    {
        $data = PageBlocks::seedTextElementsForBlock('hero', [
            'eyebrow' => 'EYEBROW',
            'heading' => 'HEADING',
            'subheading' => 'SUBHEADING',
            'cta_primary_label' => 'PRIMARY',
        ]);

        $types = array_column($data['text_elements'], 'type');
        $this->assertContains('eyebrow', $types);
        $this->assertContains('heading', $types);
        $this->assertContains('subheading', $types);
        $this->assertContains('primary_cta', $types);
    }

    public function test_seed_cta_banner_populates_text_elements_from_classic_fields(): void
    {
        $data = PageBlocks::seedTextElementsForBlock('cta_banner', [
            'heading' => 'HEADING',
            'subheading' => 'SUBHEADING',
            'button_label' => 'CLICK ME',
        ]);

        $types = array_column($data['text_elements'], 'type');
        $this->assertContains('heading', $types);
        $this->assertContains('subheading', $types);
        $this->assertContains('button', $types);
    }

    public function test_seed_three_step_populates_text_elements_from_classic_fields(): void
    {
        $data = PageBlocks::seedTextElementsForBlock('three_step', [
            'eyebrow' => 'EYEBROW',
            'heading' => 'HEADING',
            'subheading' => 'SUBHEADING',
        ]);

        $types = array_column($data['text_elements'], 'type');
        $this->assertContains('eyebrow', $types);
        $this->assertContains('heading', $types);
        $this->assertContains('subheading', $types);
    }

    public function test_seed_does_not_overwrite_existing_text_elements(): void
    {
        $existing = [['type' => 'heading', 'text' => 'CUSTOM ORDER HEADING']];

        $data = PageBlocks::seedTextElementsForBlock('hero', [
            'heading' => 'CLASSIC HEADING',
            'text_elements' => $existing,
        ]);

        // Must not overwrite the existing repeater data
        $this->assertSame($existing, $data['text_elements']);
    }
}
