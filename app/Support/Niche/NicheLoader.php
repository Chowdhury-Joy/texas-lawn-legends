<?php

namespace App\Support\Niche;

use App\Models\Addon;
use App\Models\Lead;
use App\Models\Milestone;
use App\Models\ProgressPhoto;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Loads (or resets) an industry showcase pack on this install.
 */
final class NicheLoader
{
    public function load(string $nicheId, bool $demoMode = true): NichePack
    {
        $packs = config('niche.packs', []);

        if (! isset($packs[$nicheId]) || ! is_string($packs[$nicheId])) {
            throw new InvalidArgumentException("Unknown niche pack [{$nicheId}].");
        }

        NicheResolver::flush();

        /** @var NichePack $pack */
        $pack = app($packs[$nicheId]);

        DB::transaction(function () use ($pack, $nicheId, $demoMode) {
            $this->wipeShowcaseContent();
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

    private function wipeShowcaseContent(): void
    {
        $demoLeadIds = Lead::query()->where('is_demo', true)->withTrashed()->pluck('id');

        $projectIds = Project::query()
            ->where(function ($query) use ($demoLeadIds) {
                if ($demoLeadIds->isNotEmpty()) {
                    $query->whereIn('lead_id', $demoLeadIds);
                }

                $query->orWhereIn('unique_dashboard_hash', [
                    'demokesslerpark2026renovationhash01',
                    'democleaninghydepark2026hash01',
                    'demoroofingalamoheights2026hash01',
                ]);
            })
            ->pluck('id');

        if ($projectIds->isNotEmpty()) {
            ProgressPhoto::query()->whereIn('project_id', $projectIds)->delete();
            Milestone::query()->whereIn('project_id', $projectIds)->delete();
            Project::query()->whereIn('id', $projectIds)->forceDelete();
        }

        Lead::query()->where('is_demo', true)->withTrashed()->forceDelete();

        // Known lawn demo email from before is_demo existed.
        Lead::query()->whereIn('email', [
            'demo.client@example.com',
            'demo.cleaning@example.com',
            'demo.roofing@example.com',
        ])->withTrashed()->forceDelete();

        Service::query()->delete();
        Addon::query()->delete();
        Testimonial::query()->delete();
    }

    private function applySettings(NichePack $pack): void
    {
        foreach ($pack->settingsDefaults() as [$key, $value, $type, $group]) {
            Setting::set($key, $value, $type, $group);
        }
    }
}
