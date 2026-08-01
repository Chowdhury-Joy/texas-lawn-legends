<?php

namespace Tests\Feature;

use App\Enums\LicenseTrack;
use App\Enums\UserRole;
use App\Filament\Pages\ManageDataExport;
use App\Models\Lead;
use App\Models\Setting;
use App\Models\User;
use App\Services\DataExportService;
use App\Support\AccessPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

/**
 * X-01 — self-serve full data export, Track A only.
 */
class DataExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        config(['license.track' => 'a']);
    }

    /**
     * @return array<string, string> archive entry name => contents
     */
    private function entriesOf(string $path): array
    {
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path) === true, "could not open {$path}");

        $entries = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $entries[$name] = (string) $zip->getFromIndex($i);
        }

        $zip->close();

        return $entries;
    }

    public function test_track_defaults_to_the_locked_down_track_when_unset(): void
    {
        config(['license.track' => null]);

        $this->assertSame(LicenseTrack::B, LicenseTrack::current());
        $this->assertFalse(DataExportService::selfServeAllowed());

        config(['license.track' => 'nonsense']);

        $this->assertSame(LicenseTrack::B, LicenseTrack::current());
    }

    public function test_track_a_enables_self_serve_export(): void
    {
        $this->assertSame(LicenseTrack::A, license_track());
        $this->assertTrue(LicenseTrack::A->allowsSelfServeExport());
        $this->assertFalse(LicenseTrack::B->allowsSelfServeExport());
    }

    public function test_archive_holds_a_csv_per_table_plus_readme_and_manifest(): void
    {
        Lead::factory()->create(['name' => 'Dana Ortiz', 'email' => 'dana@example.com']);

        $path = app(DataExportService::class)->generate();
        $entries = $this->entriesOf($path);

        $this->assertArrayHasKey('README.txt', $entries);
        $this->assertArrayHasKey('manifest.json', $entries);

        foreach (DataExportService::tables() as $table) {
            $this->assertArrayHasKey("data/{$table}.csv", $entries, "{$table} missing from the export");
        }

        $leads = $entries['data/leads.csv'];
        $this->assertStringContainsString('name,email', $leads);
        $this->assertStringContainsString('Dana Ortiz', $leads);
        $this->assertStringContainsString('dana@example.com', $leads);

        $manifest = json_decode($entries['manifest.json'], true);
        $this->assertSame(DataExportService::FORMAT_VERSION, $manifest['version']);
        $this->assertSame(1, $manifest['tables']['leads']);
        $this->assertSame('a', $manifest['site']['licence_track']);
    }

    public function test_uploaded_files_ride_along_at_their_public_disk_paths(): void
    {
        Storage::disk('public')->put('progress-photos/before.jpg', 'binary-ish');
        Storage::disk('public')->put('branding/logo.svg', '<svg/>');

        $entries = $this->entriesOf(app(DataExportService::class)->generate());

        $this->assertSame('binary-ish', $entries['uploads/progress-photos/before.jpg']);
        $this->assertSame('<svg/>', $entries['uploads/branding/logo.svg']);

        $manifest = json_decode($entries['manifest.json'], true);
        $this->assertSame(2, $manifest['uploads']['count']);
    }

    public function test_archive_is_named_and_titled_for_the_business_not_the_framework(): void
    {
        Setting::set('site_name', 'BrightSide Cleaning', 'string', 'general');

        $path = app(DataExportService::class)->generate();

        $this->assertStringContainsString('brightside-cleaning-data-export-', basename($path));

        $entries = $this->entriesOf($path);
        $this->assertStringStartsWith('BrightSide Cleaning — full data export', $entries['README.txt']);

        $manifest = json_decode($entries['manifest.json'], true);
        $this->assertSame('BrightSide Cleaning', $manifest['site']['name']);
    }

    public function test_framework_dotfiles_on_the_public_disk_are_not_shipped_as_uploads(): void
    {
        Storage::disk('public')->put('.gitignore', '*');
        Storage::disk('public')->put('branding/logo.svg', '<svg/>');

        $summary = app(DataExportService::class)->summary();
        $entries = $this->entriesOf(app(DataExportService::class)->generate());

        $this->assertArrayNotHasKey('uploads/.gitignore', $entries);
        $this->assertArrayHasKey('uploads/branding/logo.svg', $entries);

        $this->assertSame(1, $summary['uploads']['count'], 'the page summary must match what ships');
        $this->assertSame(1, json_decode($entries['manifest.json'], true)['uploads']['count']);
    }

    public function test_password_hashes_never_leave_the_install(): void
    {
        $user = User::factory()->create(['role' => UserRole::Admin, 'password' => 'super-secret-hash-source']);

        $entries = $this->entriesOf(app(DataExportService::class)->generate());
        $users = $entries['data/users.csv'];

        $this->assertStringContainsString($user->email, $users);
        $this->assertStringNotContainsString('password', explode("\n", $users)[0]);
        $this->assertStringNotContainsString($user->getAuthPassword(), $users);
    }

    public function test_export_is_written_to_private_storage_and_recorded_in_the_activity_log(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $path = app(DataExportService::class)->generate($admin);

        $this->assertStringContainsString('exports/', $path);
        $this->assertFileExists($path);

        $this->assertDatabaseHas('activity_log', [
            'description' => 'Downloaded a full data export',
            'causer_id' => $admin->getKey(),
        ]);
    }

    public function test_track_b_refuses_to_build_an_export(): void
    {
        config(['license.track' => 'b']);

        $this->assertFalse(DataExportService::selfServeAllowed());

        $this->expectException(RuntimeException::class);

        app(DataExportService::class)->generate();
    }

    public function test_admin_can_open_the_export_page_and_download(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/admin/manage-data-export')
            ->assertOk()
            ->assertSee('Download export');

        Livewire::actingAs($admin)
            ->test(ManageDataExport::class)
            ->callAction('export')
            ->assertHasNoActionErrors()
            ->assertFileDownloaded();
    }

    public function test_track_b_page_hides_the_button_and_explains_the_route(): void
    {
        config(['license.track' => 'b']);

        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/admin/manage-data-export')
            ->assertOk()
            ->assertSee('Self-serve export is not enabled on this plan')
            ->assertDontSee('Download export (.zip)');

        Livewire::actingAs($admin)
            ->test(ManageDataExport::class)
            ->assertActionHidden('export');
    }

    public function test_non_admin_roles_cannot_reach_the_export(): void
    {
        $this->assertSame(
            [UserRole::Admin],
            AccessPermissions::all()['settings.data_export']['roles']
        );

        foreach ([UserRole::Bookkeeper, UserRole::Operations, UserRole::Content] as $role) {
            $staff = User::factory()->create(['role' => $role]);

            $this->assertFalse($staff->canAccessKey('settings.data_export'));

            $this->actingAs($staff)
                ->get('/admin/manage-data-export')
                ->assertForbidden();
        }
    }
}
