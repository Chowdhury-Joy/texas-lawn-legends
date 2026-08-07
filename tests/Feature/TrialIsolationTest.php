<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use App\Services\TrialProvisioner;
use App\Support\Trial\TrialWorkspaceContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrialIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        TrialWorkspaceContext::clear();

        config([
            'trial.enabled' => true,
            'trial.duration_days' => 15,
            'niche.demo_hub' => false,
        ]);
    }

    protected function tearDown(): void
    {
        TrialWorkspaceContext::clear();

        parent::tearDown();
    }

    public function test_two_workspaces_do_not_share_pages(): void
    {
        $provisioner = app(TrialProvisioner::class);

        [, $pat] = $provisioner->provision('roofing', [
            'name' => 'Pat Owner',
            'email' => 'pat@example.com',
            'password' => 'secret-pass-123',
        ], 'pat-roofing');

        [, $bob] = $provisioner->provision('cleaning', [
            'name' => 'Bob Owner',
            'email' => 'bob@example.com',
            'password' => 'secret-pass-456',
        ], 'bob-cleaning');

        TrialWorkspaceContext::set($pat->fresh());
        $patPageCount = Page::query()->count();
        $patPageIds = Page::query()->pluck('id')->all();

        TrialWorkspaceContext::set($bob->fresh());
        $bobPageCount = Page::query()->count();
        $bobPageIds = Page::query()->pluck('id')->all();

        $this->assertGreaterThan(0, $patPageCount);
        $this->assertGreaterThan(0, $bobPageCount);
        $this->assertSame([], array_intersect($patPageIds, $bobPageIds));

        TrialWorkspaceContext::set($pat->fresh());
        $this->assertSame($patPageCount, Page::query()->count());
    }

    public function test_expiry_on_one_workspace_does_not_block_the_other(): void
    {
        $provisioner = app(TrialProvisioner::class);

        [, $pat] = $provisioner->provision('roofing', [
            'name' => 'Pat Owner',
            'email' => 'pat@example.com',
            'password' => 'secret-pass-123',
        ], 'pat-roofing');

        [, $bob] = $provisioner->provision('cleaning', [
            'name' => 'Bob Owner',
            'email' => 'bob@example.com',
            'password' => 'secret-pass-456',
        ], 'bob-cleaning');

        $pat->update(['expires_at' => now()->subDay()]);

        $patUser = User::withoutTrialWorkspaceScope()->where('email', 'pat@example.com')->firstOrFail();
        $bobUser = User::withoutTrialWorkspaceScope()->where('email', 'bob@example.com')->firstOrFail();

        TrialWorkspaceContext::set($pat->fresh());
        $this->assertFalse($patUser->canAccessPanel(filament()->getDefaultPanel()));

        TrialWorkspaceContext::set($bob->fresh());
        $this->assertTrue($bobUser->canAccessPanel(filament()->getDefaultPanel()));
    }

    public function test_reserved_slug_is_not_used_for_workspace(): void
    {
        $provisioner = app(TrialProvisioner::class);

        [, $workspace] = $provisioner->provision('roofing', [
            'name' => 'Signup Trap',
            'email' => 'trap@example.com',
            'password' => 'secret-pass-123',
        ], 'signup');

        $this->assertNotSame('signup', $workspace->slug);
    }
}
