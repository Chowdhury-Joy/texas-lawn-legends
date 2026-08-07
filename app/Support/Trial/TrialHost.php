<?php

namespace App\Support\Trial;

use App\Models\TrialWorkspace;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;

/**
 * V3 trial host helpers — Step 2 uses per-workspace isolation.
 */
final class TrialHost
{
    public static function enabled(): bool
    {
        return (bool) config('trial.enabled', false);
    }

    public static function durationDays(): int
    {
        return max(1, (int) config('trial.duration_days', 15));
    }

    public static function googleConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }

    /**
     * Agency marketing homepage shows whenever the trial host flag is on.
     */
    public static function showsAgencyHome(): bool
    {
        return self::enabled();
    }

    public static function current(): ?TrialWorkspace
    {
        return TrialWorkspaceContext::current();
    }

    /**
     * Whether a workspace-scoped trial is active in the current request.
     */
    public static function inWorkspace(): bool
    {
        return self::current() !== null;
    }

    /**
     * @deprecated Step 1 global flag — use TrialWorkspaceContext::current() instead.
     */
    public static function isProvisioned(): bool
    {
        return self::inWorkspace();
    }

    public static function expiresAt(): ?CarbonInterface
    {
        return self::current()?->expiresAt();
    }

    public static function isExpired(): bool
    {
        $workspace = self::current();

        return $workspace !== null && $workspace->isExpired();
    }

    public static function daysRemaining(): ?int
    {
        $workspace = self::current();

        if ($workspace === null) {
            return null;
        }

        return $workspace->daysRemaining();
    }

    public static function slug(): ?string
    {
        return self::current()?->slug;
    }

    public static function ownerEmail(): ?string
    {
        return self::current()?->owner?->email;
    }

    /**
     * @return list<string>
     */
    public static function reservedSlugs(): array
    {
        return [
            'signup',
            'niche',
            'auth',
            'admin',
            'agency',
            'demo',
            'estimate',
            'portal',
            'dashboard',
            'proposals',
            'invoices',
            'api',
            'robots.txt',
            'sitemap.xml',
        ];
    }

    public static function isReservedSlug(string $slug): bool
    {
        return in_array(Str::lower($slug), self::reservedSlugs(), true);
    }

    public static function normalizeSlug(string $raw): string
    {
        $slug = Str::slug($raw);

        if ($slug === '' || self::isReservedSlug($slug)) {
            $slug = 'trial-'.Str::lower(Str::random(6));
        }

        return Str::limit($slug, 48, '');
    }

    public static function makeUniqueSlug(string $raw): string
    {
        $base = self::normalizeSlug($raw);
        $slug = $base;
        $suffix = 2;

        while (TrialWorkspace::query()->where('slug', $slug)->exists()) {
            $slug = Str::limit($base.'-'.$suffix, 48, '');
            $suffix++;
        }

        return $slug;
    }

    public static function createWorkspace(string $nicheId, string $slug): TrialWorkspace
    {
        return TrialWorkspace::query()->create([
            'slug' => $slug,
            'niche_id' => $nicheId,
            'expires_at' => now()->addDays(self::durationDays()),
            'product_part' => 3,
            'demo_mode' => true,
        ]);
    }
}
