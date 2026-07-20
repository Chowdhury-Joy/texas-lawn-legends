<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FloatingAdminSaveButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_manage_homepage_renders_floating_save_action_bar(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/manage-homepage');

        $response->assertStatus(200);
        $response->assertSee('sticky bottom-6 z-30', false);
        $response->assertSee('Save changes');
    }

    public function test_settings_pages_render_floating_save_action_bar(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/manage-branding');

        $response->assertStatus(200);
        $response->assertSee('sticky bottom-6 z-30', false);
        $response->assertSee('Save changes');
    }
}
