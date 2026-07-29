<?php

namespace App\Support\Niche;

use App\Models\AccessCode;
use App\Models\Addon;
use App\Models\Crew;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lead;
use App\Models\MaintenanceLog;
use App\Models\Milestone;
use App\Models\Page;
use App\Models\ProgressPhoto;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\TimeEntry;
use App\Models\User;
use Database\Seeders\AccessCodesSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Loads (or restores) an industry showcase pack on the Getwebfield sales demo install.
 *
 * Restore = wipe mid-pitch mutable data, re-apply pack settings, re-run content seeders.
 */
final class NicheLoader
{
    /** Settings keys cleared before pack defaults are applied (uploads / pitch edits). */
    private const SHOWCASE_UPLOAD_SETTING_KEYS = [
        'logo_image',
        'favicon',
        'og_image',
        'hero_media_image',
    ];

    /** Staff logins preserved across restore (seeded admin). */
    private const PRESERVED_USER_EMAILS = [
        'admin@admin.com',
    ];

    public function load(string $nicheId, bool $demoMode = true): NichePack
    {
        $this->ensureDemoInstall();

        $packs = config('niche.packs', []);

        if (! isset($packs[$nicheId]) || ! is_string($packs[$nicheId])) {
            throw new InvalidArgumentException("Unknown niche pack [{$nicheId}].");
        }

        NicheResolver::flush();

        /** @var NichePack $pack */
        $pack = app($packs[$nicheId]);

        DB::transaction(function () use ($pack, $nicheId, $demoMode) {
            $this->wipeModelHomeContent();
            $this->resetShowcaseUploadSettings();
            $this->applySettings($pack);
            Setting::set('active_niche', $nicheId, 'string', 'product');
            Setting::set('demo_mode', $demoMode ? '1' : '0', 'boolean', 'product');
        });

        NicheResolver::flush();

        foreach ($pack->contentSeeders() as $seeder) {
            Artisan::call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);
        }

        Artisan::call('db:seed', [
            '--class' => AccessCodesSeeder::class,
            '--force' => true,
        ]);

        NicheResolver::flush();

        return NicheResolver::active();
    }

    public function reset(): NichePack
    {
        return $this->load(NicheResolver::active()->id(), demoMode: true);
    }

    /**
     * @return list<array{id: string, label: string, blurb: string}>
     */
    public static function hubCards(): array
    {
        $cards = [];

        foreach (config('niche.packs', []) as $id => $class) {
            /** @var NichePack $pack */
            $pack = app($class);
            $cards[] = [
                'id' => $pack->id(),
                'label' => $pack->label(),
                'blurb' => $pack->hubBlurb(),
            ];
        }

        return $cards;
    }

    /**
     * Model-home restore only runs on demo installs (APP_DEMO_HUB) or in tests.
     */
    private function ensureDemoInstall(): void
    {
        if (app()->runningUnitTests() || NicheResolver::demoHubEnabled()) {
            return;
        }

        throw new RuntimeException(
            'Model home restore is only available when APP_DEMO_HUB is enabled on this install.',
        );
    }

    /**
     * Clear all showcase data that can change during a sales pitch.
     */
    private function wipeModelHomeContent(): void
    {
        InvoiceItem::query()->delete();
        Invoice::query()->withTrashed()->forceDelete();
        Proposal::query()->withTrashed()->forceDelete();
        TimeEntry::query()->delete();
        MaintenanceLog::query()->delete();
        Equipment::query()->delete();
        ProgressPhoto::query()->delete();
        Milestone::query()->delete();

        Project::query()->withTrashed()->update(['crew_id' => null]);
        Project::query()->withTrashed()->forceDelete();

        Lead::query()->withTrashed()->forceDelete();

        Crew::query()->withTrashed()->forceDelete();

        Service::query()->delete();
        Addon::query()->delete();
        Testimonial::query()->delete();
        Page::query()->delete();
        AccessCode::query()->delete();

        User::query()
            ->whereNotIn('email', self::PRESERVED_USER_EMAILS)
            ->each(function (User $user): void {
                $user->permissions()->delete();
                $user->delete();
            });

        DB::table('activity_log')->delete();
        DB::table('notifications')->delete();
    }

    private function resetShowcaseUploadSettings(): void
    {
        foreach (self::SHOWCASE_UPLOAD_SETTING_KEYS as $key) {
            Setting::query()->where('key', $key)->delete();
            Cache::forget("setting.{$key}");
        }
    }

    private function applySettings(NichePack $pack): void
    {
        foreach ($pack->settingsDefaults() as [$key, $value, $type, $group]) {
            Setting::set($key, $value, $type, $group);
        }
    }
}
