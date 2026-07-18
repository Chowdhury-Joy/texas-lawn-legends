<?php

namespace App\Services;

use App\Models\Service;

/**
 * Algebraic estimate engine.
 *
 * Low  = base_rate_per_sqft × service_multiplier × sqft × neighborhood_modifier × complexity_modifier
 * High = Low × high_multiplier
 */
class EstimatePricingEngine
{
    /**
     * @return array{low: float, high: float, is_custom: bool}
     */
    public function calculate(Service $service, int $sqft, string $neighborhood, string $complexity): array
    {
        $baseRate = (float) setting('price_per_sqft_modifier', 3.25);
        $serviceMultiplier = (float) $service->base_price_multiplier;

        $neighborhoodModifiers = (array) setting('neighborhood_modifiers', []);
        $neighborhoodModifier = (float) ($neighborhoodModifiers[$neighborhood] ?? 1.0);

        $complexityModifiers = (array) setting('complexity_modifiers', []);
        $complexityModifier = (float) ($complexityModifiers[$complexity] ?? 1.0);

        $highMultiplier = (float) setting('estimate_high_multiplier', 1.25);

        $low = $baseRate * $serviceMultiplier * $sqft * $neighborhoodModifier * $complexityModifier;
        $high = $low * $highMultiplier;

        $customThreshold = (float) setting('estimate_custom_threshold', 25000);
        $maxSqft = (int) setting('estimate_max_sqft', 10000);

        $isCustom = ($high > $customThreshold) || ($sqft >= $maxSqft);

        return [
            'low' => round($low, 2),
            'high' => round($high, 2),
            'is_custom' => $isCustom,
        ];
    }
}
