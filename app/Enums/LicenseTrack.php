<?php

namespace App\Enums;

/**
 * How this install was sold (see docs/pricing-master.md).
 *
 * Track A — "Own it": one-time purchase, client may host anywhere, and the
 * admin can pull a full self-serve data export (suggestions X-01).
 * Track B — "Rent it": monthly subscription on our infrastructure. No
 * self-serve export; a Getwebfield super-admin runs it on request, and the
 * capability unlocks permanently after a buy-out to Track A.
 *
 * This is a per-install licence fact, not a CMS setting: it lives in the
 * environment (APP_LICENSE_TRACK) so a Track B client cannot toggle their
 * own export on from inside the admin panel.
 */
enum LicenseTrack: string
{
    case A = 'a';
    case B = 'b';

    /** Track configured for this install, defaulting to the locked-down track. */
    public static function current(): self
    {
        return self::tryFrom(strtolower(trim((string) config('license.track')))) ?? self::B;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::A => 'Track A — Own it',
            self::B => 'Track B — Rent it',
        };
    }

    /** Whether the client's own admins may download a full data export. */
    public function allowsSelfServeExport(): bool
    {
        return $this === self::A;
    }
}
