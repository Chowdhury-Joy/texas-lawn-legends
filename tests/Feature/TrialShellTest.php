<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Lead;
use App\Models\Page;
use App\Models\TrialWorkspace;
use App\Models\User;
use App\Support\Niche\NicheResolver;
use App\Support\Trial\TrialHost;
use App\Support\Trial\TrialWorkspaceContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TrialShellTest extends TestCase
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

    public function test_agency_homepage_shows_when_trial_host_enabled(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Getwebfield', false)
            ->assertSee('Start 15-day trial', false);
    }

    public function test_signup_form_and_niche_picker_flow_provisions_workspace(): void
    {
        $this->get(route('trial.signup'))
            ->assertOk()
            ->assertSee('Create your account', false);

        $this->post(route('trial.signup.store'), [
            'name' => 'Pat Owner',
            'email' => 'pat@example.com',
            'password' => 'secret-pass-123',
            'business_slug' => 'pat-roofing',
        ])->assertRedirect(route('trial.niche'));

        $this->get(route('trial.niche'))
            ->assertOk()
            ->assertSee('Choose your industry skin', false);

        $this->post(route('trial.niche.store'), [
            'niche' => 'roofing',
        ])->assertRedirect('/trial/pat-roofing/admin');

        $workspace = TrialWorkspace::query()->where('slug', 'pat-roofing')->first();
        $this->assertNotNull($workspace);
        $this->assertSame('roofing', $workspace->niche_id);
        $this->assertFalse($workspace->isExpired());

        TrialWorkspaceContext::set($workspace);

        $this->assertTrue(NicheResolver::demoMode());
        $this->assertSame('roofing', NicheResolver::activeId());
        $this->assertSame(3, (int) setting('product_part'));

        $user = User::query()->where('email', 'pat@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame(UserRole::Admin, $user->role);
        $this->assertSame($workspace->id, $user->trial_workspace_id);
        $this->assertGreaterThan(
            0,
            Lead::withoutTrialWorkspaceScope()->where('trial_workspace_id', $workspace->id)->count(),
        );
        $this->assertTrue(Auth::check());
        $this->assertTrue($user->canAccessPanel(filament()->getDefaultPanel()));

        $this->get('/trial/pat-roofing')
            ->assertOk()
            ->assertSee('Demo purpose only', false);

        $this->get('/')
            ->assertOk()
            ->assertSee('Start 15-day trial', false);
    }

    public function test_expired_trial_blocks_admin_panel_access(): void
    {
        $workspace = TrialWorkspace::query()->create([
            'slug' => 'expired',
            'niche_id' => 'roofing',
            'expires_at' => now()->subDay(),
            'product_part' => 3,
            'demo_mode' => true,
        ]);

        $user = User::factory()->create([
            'email' => 'gone@example.com',
            'role' => UserRole::Admin,
            'trial_workspace_id' => $workspace->id,
        ]);

        $workspace->update(['owner_user_id' => $user->id]);

        TrialWorkspaceContext::set($workspace->fresh());

        $this->assertTrue($workspace->isExpired());
        $this->assertFalse($user->canAccessPanel(filament()->getDefaultPanel()));
    }

    public function test_second_signup_allowed_after_first_workspace(): void
    {
        $this->post(route('trial.signup.store'), [
            'name' => 'Pat Owner',
            'email' => 'pat@example.com',
            'password' => 'secret-pass-123',
            'business_slug' => 'pat-roofing',
        ]);

        $this->post(route('trial.niche.store'), ['niche' => 'roofing']);

        $this->get(route('trial.signup'))
            ->assertOk();

        $this->post(route('trial.signup.store'), [
            'name' => 'Bob Owner',
            'email' => 'bob@example.com',
            'password' => 'secret-pass-456',
            'business_slug' => 'bob-cleaning',
        ])->assertRedirect(route('trial.niche'));

        $this->post(route('trial.niche.store'), ['niche' => 'cleaning'])
            ->assertRedirect('/trial/bob-cleaning/admin');

        $this->assertSame(2, TrialWorkspace::query()->count());
    }

    public function test_agency_routes_404_when_trial_host_disabled(): void
    {
        config(['trial.enabled' => false]);

        $this->get('/agency')->assertNotFound();
        $this->get(route('trial.signup'))->assertNotFound();
    }
}
