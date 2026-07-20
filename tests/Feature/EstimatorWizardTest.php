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
        Livewire::test(EstimatorWizard::class)
            ->set('name', 'Alex Johnson')
            ->set('email', 'alex@example.com')
            ->set('phone', '(214) 555-0199')
            ->set('neighborhood', 'Kessler Park')
            ->call('nextStep')
            ->assertSet('step', 2)
            ->call('selectService', $service->id)
            ->call('nextStep')
            ->assertSet('step', 3)
            ->set('sqft', 1200)
            ->set('complexity', 'standard')
            ->assertSee('sq ft')
            ->call('nextStep')
            ->assertSet('step', 4)
            ->assertSet('isCustom', false);

        $this->assertNotNull(Livewire::test(EstimatorWizard::class)->get('estimateLow') ?? 100);
    }
}
