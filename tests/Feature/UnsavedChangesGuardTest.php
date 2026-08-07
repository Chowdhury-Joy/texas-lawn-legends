<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A big Builder form is easy to lose to an accidental navigation or a
 * session timeout, so every custom editor view wires a beforeunload guard
 * onto its <form>: track edits via native input/change events, and refuse
 * to unload while they're unsaved. Verified live in a real browser (typed
 * input flips the guard on, a real save click flips it back off) — this
 * suite locks in that the markup enabling that behaviour keeps shipping.
 *
 * There is deliberately no visible "unsaved changes" badge: an x-show bound
 * to the same dirty flag was tried and dropped after it was found getting
 * stuck in a stale visual state across a Livewire morph (e.g. immediately
 * after this same form's own save request) — see the comment above each
 * <form> tag.
 */
class UnsavedChangesGuardTest extends TestCase
{
    use RefreshDatabase;

    private function assertGuardsAgainstUnsavedNavigation(string $html): void
    {
        $this->assertStringContainsString('x-data="{ dirty: false }"', $html);
        $this->assertStringContainsString("addEventListener('beforeunload'", $html);
        $this->assertStringContainsString('@input.capture="dirty = true"', $html);
        $this->assertStringContainsString('@change.capture="dirty = true"', $html);
        $this->assertStringContainsString('@submit="dirty = false"', $html);
    }

    public function test_manage_homepage_has_the_unsaved_changes_guard(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->assertGuardsAgainstUnsavedNavigation(
            $this->actingAs($admin)->get('/admin/manage-homepage')->assertStatus(200)->getContent()
        );
    }

    public function test_settings_pages_have_the_unsaved_changes_guard(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->assertGuardsAgainstUnsavedNavigation(
            $this->actingAs($admin)->get('/admin/manage-branding')->assertStatus(200)->getContent()
        );
    }

    public function test_page_create_and_edit_screens_have_the_unsaved_changes_guard(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->assertGuardsAgainstUnsavedNavigation(
            $this->actingAs($admin)->get('/admin/pages/create')->assertStatus(200)->getContent()
        );

        $page = Page::create(['title' => 'Guard Check', 'slug' => 'guard-check', 'is_published' => true, 'blocks' => []]);

        $this->assertGuardsAgainstUnsavedNavigation(
            $this->actingAs($admin)->get("/admin/pages/{$page->slug}/edit")->assertStatus(200)->getContent()
        );
    }
}
