<?php

namespace Tests\Feature;

use App\Enums\ServiceCategory;
use App\Livewire\EstimatorWizard;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EstimatorWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_estimator_wizard_renders_and_computes_estimates(): void
    {
        $service = Service::factory()->create([
            'title' => 'Custom Hardscaping',
            'slug' => 'custom-hardscaping',
            'category' => ServiceCategory::Create,
            'short_description' => 'Patio & retaining wall builds',
            'long_description' => 'Full custom patio build.',
            'icon' => 'sparkles',
            'is_active' => true,
            'base_price_multiplier' => 1.5,
        ]);
        Setting::set('service_areas', ['Kessler Park'], 'array');
        Setting::set('complexity_modifiers', [
            'simple' => 0.85,
            'standard' => 1.00,
            'complex' => 1.30,
        ], 'json', 'pricing');
        Setting::set('neighborhood_modifiers', ['Kessler Park' => 1.15], 'json', 'pricing');
        Setting::set('price_per_sqft_modifier', 3.25, 'decimal', 'pricing');

        Livewire::test(EstimatorWizard::class)
            ->set('neighborhood', 'Kessler Park')
            ->call('selectService', $service->id)
            ->call('nextStep')
            ->assertSet('step', 2)
            ->set('sqft', 1200)
            ->set('complexity', 'standard')
            ->assertSee('sq ft')
            ->call('nextStep')
            ->assertSet('step', 3)
            ->set('name', 'Alex Johnson')
            ->set('email', 'alex@example.com')
            ->set('phone', '(214) 555-0199')
            ->call('nextStep')
            ->assertSet('step', 4)
            ->assertSet('isCustom', false)
            ->assertNotSet('estimateLow', null);
    }
}
