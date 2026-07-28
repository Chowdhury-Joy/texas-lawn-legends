<?php

namespace App\Support\Niche;

interface NichePack
{
    /**
     * Machine id used in config (e.g. "lawn").
     */
    public function id(): string;

    /**
     * Human label for admin / docs (e.g. "Lawn & landscaping").
     */
    public function label(): string;

    /**
     * Schema.org @type for local business JSON-LD.
     */
    public function schemaOrgType(): string;

    /**
     * Pricing strategy key (lawn uses sqft_neighborhood).
     */
    public function pricingStrategy(): string;

    /**
     * Industry vocabulary keyed for niche_label().
     *
     * @return array<string, string>
     */
    public function labels(): array;

    /**
     * First-run setting defaults for this industry (brand/demo values).
     * Core product keys (product_part, empty brand skeleton) stay in SettingsSeeder.
     *
     * @return array<int, array{0:string,1:mixed,2:string,3:string}>
     */
    public function settingsDefaults(): array;

    /**
     * Content seeder classes (services, pages, testimonials, etc.).
     *
     * @return list<class-string<\Illuminate\Database\Seeder>>
     */
    public function contentSeeders(): array;

    /**
     * One-line pitch for the public demo hub card.
     */
    public function hubBlurb(): string;
}
