<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\ProjectStatus;
use App\Enums\ServiceCategory;
use App\Enums\UserRole;
use App\Models\Crew;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Support\Niche\NicheLoader;
use App\Support\Niche\NicheResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NicheModelHomeRestoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_restore_clears_mid_pitch_edits_and_reapplies_pack_defaults(): void
    {
        config(['niche.demo_hub' => true]);

        User::factory()->create([
            'email' => 'admin@admin.com',
            'role' => UserRole::Admin,
        ]);

        app(NicheLoader::class)->load('lawn', demoMode: true);
        NicheResolver::flush();

        $seedServiceCount = Service::query()->count();
        $seedProjectCount = Project::query()->count();

        Setting::set('site_name', 'Pitch Edited Brand', 'string', 'general');
        Setting::set('logo_image', 'branding/pitch-logo.png', 'string', 'branding');

        Service::factory()->create([
            'title' => 'Pitch-Only Service',
            'slug' => 'pitch-only-service',
            'category' => ServiceCategory::Create,
            'icon' => 'sparkles',
            'short_description' => 'Added during a sales call',
            'long_description' => 'Pitch-only',
            'is_active' => true,
        ]);

        $pitchLead = Lead::create([
            'name' => 'Pitch Prospect',
            'email' => 'pitch.prospect@example.com',
            'phone' => '(214) 555-0199',
            'neighborhood' => 'Kessler Park',
            'status' => LeadStatus::Partial,
            'is_demo' => false,
        ]);

        Project::factory()->create([
            'lead_id' => $pitchLead->id,
            'client_name' => 'Pitch Prospect',
            'project_title' => 'Extra Pitch Project',
            'neighborhood' => 'Kessler Park',
            'contract_value' => 5000,
            'status' => ProjectStatus::Active,
            'started_at' => now()->toDateString(),
        ]);

        Crew::create(['name' => 'Pitch Crew']);

        User::factory()->create([
            'email' => 'sales.pitch@example.com',
            'role' => UserRole::Sales,
        ]);

        Page::create([
            'title' => 'Pitch Custom Page',
            'slug' => 'pitch-custom-page',
            'is_published' => true,
            'blocks' => [],
        ]);

        $this->assertSame($seedProjectCount + 1, Project::query()->count());
        $this->assertSame($seedServiceCount + 1, Service::query()->count());

        app(NicheLoader::class)->reset();
        NicheResolver::flush();

        $this->assertSame('Texas Lawn Legends', setting('site_name'));
        $this->assertNull(setting('logo_image'));
        $this->assertFalse(Service::query()->where('title', 'Pitch-Only Service')->exists());
        $this->assertSame($seedServiceCount, Service::query()->count());
        $this->assertFalse(Lead::query()->where('email', 'pitch.prospect@example.com')->exists());
        $this->assertFalse(Project::query()->where('project_title', 'Extra Pitch Project')->exists());
        $this->assertSame($seedProjectCount, Project::query()->count());
        $this->assertFalse(Crew::query()->where('name', 'Pitch Crew')->exists());
        $this->assertFalse(User::query()->where('email', 'sales.pitch@example.com')->exists());
        $this->assertTrue(User::query()->where('email', 'admin@admin.com')->exists());
        $this->assertFalse(Page::query()->where('slug', 'pitch-custom-page')->exists());
        $this->assertTrue(Page::query()->where('is_home', true)->exists());
    }
}
