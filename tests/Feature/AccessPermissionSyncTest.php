<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use App\Support\AccessPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Covers the per-user Access matrix: the grant/revoke overrides layered on
 * top of a role's default preset. The role presets themselves are covered
 * by RbacAccessTest.
 */
class AccessPermissionSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
    }

    private function editUser(User $user, array $accessKeys): void
    {
        Livewire::test(EditUser::class, ['record' => $user->getKey()])
            ->fillForm([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'access_keys' => $accessKeys,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh()->unsetRelation('permissions');
    }

    public function test_unchecking_a_role_default_revokes_it(): void
    {
        $user = User::factory()->create(['role' => UserRole::Content]);

        $this->assertTrue($user->canAccessKey('resource.pages'));

        // Content's preset includes pages + services; drop pages.
        $this->editUser($user, ['resource.services', 'resource.testimonials', 'resource.addons']);

        $this->assertFalse($user->canAccessKey('resource.pages'));
        $this->assertTrue($user->canAccessKey('resource.services'));
    }

    public function test_checking_a_key_beyond_the_preset_grants_it(): void
    {
        $user = User::factory()->create(['role' => UserRole::Content]);

        $this->assertFalse($user->canAccessKey('resource.leads'));

        $this->editUser($user, ['resource.pages', 'resource.services', 'resource.testimonials', 'resource.addons', 'resource.leads']);

        $this->assertTrue($user->canAccessKey('resource.leads'));
    }

    public function test_only_deltas_from_the_preset_are_persisted(): void
    {
        $user = User::factory()->create(['role' => UserRole::Content]);

        // Exactly the Content preset — nothing differs, so nothing is stored.
        $this->editUser($user, AccessPermissions::defaultsFor(UserRole::Content));

        $this->assertSame(0, $user->permissions()->count());
    }

    public function test_a_revoked_resource_returns_403(): void
    {
        $user = User::factory()->create(['role' => UserRole::Content]);

        $this->editUser($user, ['resource.services']);

        $this->actingAs($user)
            ->get('/admin/pages')
            ->assertStatus(403);
    }

    public function test_admins_never_persist_overrides_and_keep_full_access(): void
    {
        $user = User::factory()->create(['role' => UserRole::Admin]);

        $this->editUser($user, []);

        $this->assertSame(0, $user->permissions()->count());
        $this->assertTrue($user->canAccessKey('resource.leads'));
        $this->assertTrue($user->canAccessKey('settings.branding'));
    }

    public function test_grants_chosen_at_creation_are_persisted(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New Editor',
                'email' => 'editor@example.com',
                'role' => UserRole::Content->value,
                'password' => 'secret-password',
                'access_keys' => ['resource.pages', 'resource.leads'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'editor@example.com')->sole();

        $this->assertTrue($user->canAccessKey('resource.leads'), 'grant beyond preset should persist');
        $this->assertFalse($user->canAccessKey('resource.services'), 'unchecked preset key should be revoked');
    }
}
