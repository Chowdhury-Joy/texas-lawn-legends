<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Pages\ManageHomepage;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
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

    public function test_form_actions_are_sticky_on_cms_pages(): void
    {
        $homepage = new ManageHomepage;
        $this->assertTrue($homepage->areFormActionsSticky());

        $editPage = new EditPage;
        $this->assertTrue($editPage->areFormActionsSticky());

        $createPage = new CreatePage;
        $this->assertTrue($createPage->areFormActionsSticky());
    }

    public function test_homepage_builder_page_loads(): void
    {
        $user = User::factory()->create(['role' => UserRole::Admin->value]);

        $response = $this->actingAs($user)->get(route('filament.admin.pages.manage-homepage'));
        $response->assertStatus(200);
    }
}
