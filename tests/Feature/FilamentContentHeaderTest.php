<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\UserRole;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentContentHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_page_shows_title_without_back_link(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $html = $this->actingAs($admin)
            ->get('/admin/leads')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('fi-header-heading', $html);
        $this->assertStringContainsString('fi-content-shell-header', $html);
        $this->assertStringNotContainsString('fi-page-header-back', $html);
        $this->assertStringNotContainsString('fi-page-header-has-back', $html);
    }

    public function test_create_page_shows_back_link_to_resource_index(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $html = $this->actingAs($admin)
            ->get('/admin/milestones/create')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('fi-page-header-back', $html);
        $this->assertStringContainsString('href="'.route('filament.admin.resources.milestones.index').'"', $html);
        $this->assertStringContainsString('Create Milestone', $html);
    }

    public function test_edit_page_shows_back_link_to_resource_index(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $lead = Lead::create([
            'name' => 'Demo Wash Client',
            'email' => 'demo.wash@example.com',
            'phone' => '(713) 555-0118',
            'status' => LeadStatus::Booked,
        ]);

        $html = $this->actingAs($admin)
            ->get("/admin/leads/{$lead->uuid}/edit")
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('fi-page-header-back', $html);
        $this->assertStringContainsString('href="'.route('filament.admin.resources.leads.index').'"', $html);
        $this->assertStringContainsString('Demo Wash Client', $html);
    }

    public function test_edit_page_uses_save_as_header_cta_not_delete(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $lead = Lead::create([
            'name' => 'Demo Wash Client',
            'email' => 'demo.wash@example.com',
            'phone' => '(713) 555-0118',
            'status' => LeadStatus::Booked,
        ]);

        $html = $this->actingAs($admin)
            ->get("/admin/leads/{$lead->uuid}/edit")
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<header[^>]*fi-content-shell-header[\s\S]*?Save changes[\s\S]*?<\/header>/i',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<header[^>]*fi-content-shell-header[\s\S]*?>\s*Delete\s*<[\s\S]*?<\/header>/i',
            $html
        );
        $this->assertStringContainsString('Delete', $html);
    }
}
