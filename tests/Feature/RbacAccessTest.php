<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_access_settings_pages(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/admin/manage-branding')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/manage-homepage')
            ->assertStatus(200);
    }

    public function test_non_admin_users_are_forbidden_from_settings_pages(): void
    {
        $contentUser = User::factory()->create(['role' => UserRole::Content]);
        $opsUser = User::factory()->create(['role' => UserRole::Operations]);

        $this->actingAs($contentUser)
            ->get('/admin/manage-branding')
            ->assertStatus(403);

        $this->actingAs($contentUser)
            ->get('/admin/manage-homepage')
            ->assertStatus(403);

        $this->actingAs($opsUser)
            ->get('/admin/manage-branding')
            ->assertStatus(403);

        $this->actingAs($opsUser)
            ->get('/admin/manage-homepage')
            ->assertStatus(403);
    }

    public function test_content_user_can_access_site_content_resources(): void
    {
        $contentUser = User::factory()->create(['role' => UserRole::Content]);

        $this->actingAs($contentUser)
            ->get('/admin/pages')
            ->assertStatus(200);

        $this->actingAs($contentUser)
            ->get('/admin/services')
            ->assertStatus(200);

        $this->actingAs($contentUser)
            ->get('/admin/testimonials')
            ->assertStatus(200);

        $this->actingAs($contentUser)
            ->get('/admin/addons')
            ->assertStatus(200);
    }

    public function test_content_user_cannot_access_operations_resources(): void
    {
        $contentUser = User::factory()->create(['role' => UserRole::Content]);

        $this->actingAs($contentUser)
            ->get('/admin/projects')
            ->assertStatus(403);

        $this->actingAs($contentUser)
            ->get('/admin/access-codes')
            ->assertStatus(403);
    }
}
