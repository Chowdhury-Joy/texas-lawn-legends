<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FloatingAdminSaveButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_manage_homepage_renders_save_action(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/manage-homepage');

        $response->assertStatus(200);
        $response->assertSee('style="margin-top: 24px"', false);
        $response->assertSee('flex items-center justify-end', false);
        $response->assertDontSee('sticky bottom-0 z-40', false);
        $response->assertSee('Save changes');
    }

    public function test_settings_pages_render_save_action(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/manage-branding');

        $response->assertStatus(200);
        $response->assertSee('style="margin-top: 24px"', false);
        $response->assertSee('flex items-center justify-end', false);
        $response->assertDontSee('sticky bottom-0 z-40', false);
        $response->assertDontSee('Site Settings Editor');
        $response->assertSee('Save changes');
    }
}
