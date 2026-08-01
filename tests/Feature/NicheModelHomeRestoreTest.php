<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\LeadStatus;
use App\Enums\ProjectStatus;
use App\Enums\ProposalStatus;
use App\Enums\ServiceCategory;
use App\Enums\UserRole;
use App\Models\Crew;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Page;
use App\Models\ProgressPhoto;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TimeEntry;
use App\Models\User;
use App\Support\Niche\NicheLoader;
use App\Support\Niche\NicheResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function test_load_stocks_operations_and_restore_reseeds_it(): void
    {
        config(['niche.demo_hub' => true]);

        app(NicheLoader::class)->load('lawn', demoMode: true);
        NicheResolver::flush();

        $this->assertOpsDemoDataSeeded('North Dallas Crew');

        Invoice::query()->forceDelete();
        Proposal::query()->forceDelete();
        Equipment::query()->delete();
        TimeEntry::query()->delete();

        app(NicheLoader::class)->reset();
        NicheResolver::flush();

        $this->assertOpsDemoDataSeeded('North Dallas Crew');
    }

    #[DataProvider('nichePacksWithDemoCrews')]
    public function test_every_niche_pack_stocks_operations(string $nicheId, string $crewName): void
    {
        config(['niche.demo_hub' => true]);

        app(NicheLoader::class)->load($nicheId, demoMode: true);
        NicheResolver::flush();

        $this->assertOpsDemoDataSeeded($crewName);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function nichePacksWithDemoCrews(): array
    {
        return [
            'lawn' => ['lawn', 'North Dallas Crew'],
            'cleaning' => ['cleaning', 'Hyde Park Team'],
            'roofing' => ['roofing', 'Summit Install Crew'],
            'pressure' => ['pressure', 'Heights Wash Crew'],
            'windows' => ['windows', 'West 7th Glass Team'],
            'gutters' => ['gutters', 'Plano Seamless Crew'],
            'fence' => ['fence', 'Frisco Build Crew'],
            'pest' => ['pest', 'Teravista Route Team'],
        ];
    }

    private function assertOpsDemoDataSeeded(string $crewName): void
    {
        $crew = Crew::query()->where('name', $crewName)->first();
        $this->assertNotNull($crew, "Expected demo crew [{$crewName}] to be seeded.");

        $project = Project::query()->where('crew_id', $crew->id)->first();
        $this->assertNotNull($project, 'Expected the sample project to be assigned to the demo crew.');

        $proposal = Proposal::query()->where('project_id', $project->id)->first();
        $this->assertNotNull($proposal, 'Expected a demo proposal for the sample project.');
        $this->assertSame(ProposalStatus::Sent, $proposal->status);
        $this->assertSame(
            (float) $project->contract_value,
            (float) $proposal->total_amount,
        );

        $invoice = Invoice::query()->where('project_id', $project->id)->first();
        $this->assertNotNull($invoice, 'Expected a demo invoice for the sample project.');
        $this->assertSame(InvoiceStatus::Sent, $invoice->status);
        $this->assertGreaterThan(0, $invoice->items()->count());
        $this->assertSame(
            (float) $project->contract_value,
            (float) $invoice->total,
            'Invoice line items should add up to the contract value.',
        );

        $this->assertGreaterThanOrEqual(2, Equipment::query()->where('crew_id', $crew->id)->count());

        $this->assertGreaterThanOrEqual(2, TimeEntry::query()->where('project_id', $project->id)->count());

        $project = $project->fresh();
        $this->assertGreaterThan(0, (float) $project->labor_cost);
        $this->assertGreaterThan(0, (float) $project->material_cost);
        $this->assertGreaterThan(20, $project->profit_margin_percent, 'Demo profit margin should not look implausibly high.');
        $this->assertLessThan(70, $project->profit_margin_percent, 'Demo profit margin should not look implausibly high.');

        // Funnel/chart filler: 3 Partial + 3 Qualified + 3 Contacted + 1 Lost (+ booked sample).
        $this->assertGreaterThanOrEqual(3, Lead::query()->where('status', LeadStatus::Partial)->count());
        $this->assertGreaterThanOrEqual(3, Lead::query()->where('status', LeadStatus::Qualified)->count());
        $this->assertGreaterThanOrEqual(3, Lead::query()->where('status', LeadStatus::Contacted)->count());
        $this->assertGreaterThanOrEqual(1, Lead::query()->where('status', LeadStatus::Lost)->count());
        $this->assertGreaterThanOrEqual(1, Lead::query()->where('status', LeadStatus::Booked)->count());

        $this->assertTrue(User::query()->where('email', 'sales@demo.local')->exists());
        $this->assertTrue(User::query()->where('email', 'ops@demo.local')->exists());
        $this->assertTrue(User::query()->where('email', 'books@demo.local')->exists());

        $photosWithImages = ProgressPhoto::query()
            ->where('project_id', $project->id)
            ->whereNotNull('image_path')
            ->count();
        $this->assertGreaterThanOrEqual(3, $photosWithImages, 'Demo progress photos should use seeded Unsplash assets.');
    }
}
