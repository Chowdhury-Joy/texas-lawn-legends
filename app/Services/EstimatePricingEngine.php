<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Collection;

/**
 * Algebraic estimate engine.
 *
 * Lawn strategy (sqft_neighborhood):
 * Low  = base_rate_per_sqft × service_multiplier × sqft × neighborhood_modifier × complexity_modifier
 * High = Low × high_multiplier
 *
 * Strategy key comes from niche()->pricingStrategy() so future packs can swap formulas
 * without rewriting the estimator wizard steps.
 */
class EstimatePricingEngine
{
    /**
     * @return array{low: float, high: float, is_custom: bool}
     */
    public function calculate(Service $service, int $sqft, string $neighborhood, string $complexity): array
    {
        // Currently only sqft_neighborhood is implemented; other strategies can branch here later.
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

    /**
     * Calculate a combined estimate across multiple services (Full Estimate mode).
     * Sqft, neighborhood, and complexity are shared across all services.
     *
     * @param  Collection<int, Service>  $services
     * @return array{low: float, high: float, is_custom: bool}
     */
    public function calculateMany(Collection $services, int $sqft, string $neighborhood, string $complexity): array
    {
        $totalLow = 0.0;
        $totalHigh = 0.0;
        $anyCustom = false;

        foreach ($services as $service) {
            $result = $this->calculate($service, $sqft, $neighborhood, $complexity);
            $totalLow  += $result['low'];
            $totalHigh += $result['high'];
            if ($result['is_custom']) {
                $anyCustom = true;
            }
        }

        $customThreshold = (float) setting('estimate_custom_threshold', 25000);
        $maxSqft = (int) setting('estimate_max_sqft', 10000);

        if ($totalHigh > $customThreshold || $sqft >= $maxSqft) {
            $anyCustom = true;
        }

        return [
            'low'       => round($totalLow, 2),
            'high'      => round($totalHigh, 2),
            'is_custom' => $anyCustom,
        ];
    }
}
