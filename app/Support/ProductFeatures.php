<?php

namespace App\Support;

use App\Enums\ProductPart;

/**
 * Maps admin areas and public product surfaces to the minimum Product Part
 * required. Unlisted keys default to Part 1 (Website + CMS).
 */
final class ProductFeatures
{
    /**
     * @return array<string, ProductPart>
     */
    public static function map(): array
    {
        return [
            // Part 2 — Booking / leads
            'resource.leads' => ProductPart::Booking,
            'settings.pricing' => ProductPart::Booking,
            'route.estimate' => ProductPart::Booking,

            // Part 3 — Ops
            'resource.projects' => ProductPart::Ops,
            'resource.crews' => ProductPart::Ops,
            'resource.invoices' => ProductPart::Ops,
            'resource.proposals' => ProductPart::Ops,
            'resource.equipment' => ProductPart::Ops,
            'resource.time_entries' => ProductPart::Ops,
            'resource.access_codes' => ProductPart::Ops,
            'resource.progress_photos' => ProductPart::Ops,
            'resource.addons' => ProductPart::Ops,
            'resource.milestones' => ProductPart::Ops,
            'settings.operations' => ProductPart::Ops,
            'route.portal' => ProductPart::Ops,
            'route.dashboard' => ProductPart::Ops,
            'route.proposals' => ProductPart::Ops,
            'route.invoices' => ProductPart::Ops,
        ];
    }

    public static function minimumPartFor(string $key): ProductPart
    {
        return self::map()[$key] ?? ProductPart::Website;
    }

    public static function allows(string $key): bool
    {
        return product_part()->atLeast(self::minimumPartFor($key));
    }
}
