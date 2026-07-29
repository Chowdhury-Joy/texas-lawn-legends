<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\ProposalStatus;
use App\Enums\ServiceCategory;
use App\Livewire\EstimatorWizard;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\Setting;
use App\Services\BookingMatrix;
use App\Services\EstimatePricingEngine;
use App\Support\ReservedPageSlugs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class FunctionalBugFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_referral_code_from_query_string_persists_on_lead(): void
    {
        $service = $this->seedEstimatorBasics();

        Livewire::withQueryParams(['ref' => 'PARTNER-42'])
            ->test(EstimatorWizard::class)
            ->set('neighborhood', 'Kessler Park')
            ->call('selectService', $service->id)
            ->call('nextStep')
            ->assertSet('step', 2);

        $this->assertDatabaseHas('leads', [
            'referred_by_code' => 'PARTNER-42',
        ]);
    }

    public function test_unpublished_homepage_returns_404(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'is_home' => true,
            'is_published' => false,
            'blocks' => [],
        ]);

        $this->get('/')->assertNotFound();
    }

    public function test_decline_does_not_overwrite_accepted_proposal(): void
    {
        $lead = Lead::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'status' => LeadStatus::Qualified,
        ]);

        $proposal = Proposal::create([
            'lead_id' => $lead->id,
            'status' => ProposalStatus::Sent,
            'total_amount' => 2500,
            'content' => [],
        ]);

        $this->post(route('proposals.accept', ['token' => $proposal->unique_token]));

        $proposal->refresh();
        $this->assertSame(ProposalStatus::Accepted, $proposal->status);

        $this->post(route('proposals.decline', ['token' => $proposal->unique_token]));

        $proposal->refresh();
        $this->assertSame(ProposalStatus::Accepted, $proposal->status);
    }

    public function test_book_rejects_slot_not_in_booking_matrix(): void
    {
        $this->seedBookingSettings();

        $lead = Lead::create([
            'name' => 'Alex Johnson',
            'email' => 'alex@example.com',
            'phone' => '(214) 555-0199',
            'status' => LeadStatus::Qualified,
        ]);

        Livewire::test(EstimatorWizard::class)
            ->set('leadUuid', $lead->uuid)
            ->call('book', '2099-06-15', '11:00 PM')
            ->assertHasErrors('booking');

        $lead->refresh();
        $this->assertNull($lead->scheduled_at);
        $this->assertSame(LeadStatus::Qualified, $lead->status);
    }

    public function test_book_rejects_already_taken_slot(): void
    {
        Carbon::setTestNow('2026-07-30 10:00:00');
        $this->seedBookingSettings();

        $matrix = app(BookingMatrix::class);
        $slots = $matrix->slots();
        $date = $slots[0]['date'];
        $time = $slots[0]['times'][0];

        $firstLead = Lead::create([
            'name' => 'First Lead',
            'email' => 'first@example.com',
            'phone' => '(214) 555-0001',
            'status' => LeadStatus::Qualified,
        ]);

        $secondLead = Lead::create([
            'name' => 'Second Lead',
            'email' => 'second@example.com',
            'phone' => '(214) 555-0002',
            'status' => LeadStatus::Qualified,
        ]);

        Livewire::test(EstimatorWizard::class)
            ->set('leadUuid', $firstLead->uuid)
            ->call('book', $date, $time)
            ->assertSet('booked', true);

        Livewire::test(EstimatorWizard::class)
            ->set('leadUuid', $secondLead->uuid)
            ->call('book', $date, $time)
            ->assertHasErrors('booking');

        $secondLead->refresh();
        $this->assertNull($secondLead->scheduled_at);
        $this->assertSame(LeadStatus::Qualified, $secondLead->status);

        Carbon::setTestNow();
    }

    public function test_reserved_page_slugs_include_static_routes(): void
    {
        $reserved = ReservedPageSlugs::all();

        foreach (['estimate', 'portal', 'dashboard', 'demo', 'proposals', 'invoices', 'api'] as $slug) {
            $this->assertContains($slug, $reserved, "Expected {$slug} to be reserved");
        }
    }

    public function test_booking_matrix_does_not_mutate_carbon_today(): void
    {
        Carbon::setTestNow('2026-07-30 10:00:00');
        $this->seedBookingSettings();

        $todayBefore = Carbon::today()->toDateString();

        app(BookingMatrix::class)->slots();

        $this->assertSame($todayBefore, Carbon::today()->toDateString());

        Carbon::setTestNow();
    }

    public function test_estimator_mounts_sqft_at_pack_minimum_not_hardcoded_floor(): void
    {
        Setting::set('estimate_min_sqft', 10, 'integer', 'pricing');
        Setting::set('estimate_max_sqft', 80, 'integer', 'pricing');
        Setting::set('service_areas', ['Kessler Park'], 'json', 'general');
        Setting::set('complexity_modifiers', ['simple' => 0.9, 'standard' => 1.0, 'complex' => 1.4], 'json', 'pricing');

        $service = $this->seedEstimatorBasics();

        Livewire::test(EstimatorWizard::class)
            ->assertSet('sqft', 10)
            ->set('neighborhood', 'Kessler Park')
            ->call('selectService', $service->id)
            ->call('nextStep')
            ->assertSet('step', 2)
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 3);
    }

    public function test_multi_service_estimate_flags_custom_when_summed_total_exceeds_threshold(): void
    {
        Setting::set('price_per_sqft_modifier', 3.25, 'decimal', 'pricing');
        Setting::set('neighborhood_modifiers', ['Area' => 1.0], 'json', 'pricing');
        Setting::set('complexity_modifiers', ['standard' => 1.0], 'json', 'pricing');
        Setting::set('estimate_high_multiplier', 1.25, 'decimal', 'pricing');
        Setting::set('estimate_custom_threshold', 25000, 'decimal', 'pricing');
        Setting::set('estimate_max_sqft', 10000, 'integer', 'pricing');

        $services = collect([
            $this->makeProbeService('A', 'svc-a'),
            $this->makeProbeService('B', 'svc-b'),
            $this->makeProbeService('C', 'svc-c'),
            $this->makeProbeService('D', 'svc-d'),
            $this->makeProbeService('E', 'svc-e'),
        ]);

        $result = app(EstimatePricingEngine::class)->calculateMany($services, 1500, 'Area', 'standard');

        $this->assertTrue($result['is_custom']);
        $this->assertGreaterThan(25000, $result['high']);
    }

    public function test_booked_lead_status_persists_when_revisiting_steps(): void
    {
        Carbon::setTestNow('2026-07-30 10:00:00');
        $this->seedBookingSettings();
        Setting::set('service_areas', ['Kessler Park'], 'array');
        Setting::set('complexity_modifiers', ['simple' => 0.85, 'standard' => 1.0, 'complex' => 1.3], 'json', 'pricing');
        Setting::set('neighborhood_modifiers', ['Kessler Park' => 1.0], 'json', 'pricing');
        Setting::set('price_per_sqft_modifier', 3.25, 'decimal', 'pricing');
        Setting::set('estimate_min_sqft', 100, 'integer', 'pricing');
        Setting::set('estimate_max_sqft', 10000, 'integer', 'pricing');

        $service = $this->seedEstimatorBasics();

        $component = Livewire::test(EstimatorWizard::class)
            ->set('neighborhood', 'Kessler Park')
            ->call('selectService', $service->id)
            ->call('nextStep')
            ->set('sqft', 1200)
            ->set('complexity', 'standard')
            ->call('nextStep')
            ->set('name', 'Alex Johnson')
            ->set('email', 'alex@example.com')
            ->set('phone', '(214) 555-0199')
            ->call('nextStep');

        $slots = $component->get('bookingSlots');
        $date = $slots[0]['date'];
        $time = $slots[0]['times'][0];

        $component->call('book', $date, $time)->assertSet('booked', true);

        $lead = Lead::query()->where('uuid', $component->get('leadUuid'))->first();
        $this->assertSame(LeadStatus::Booked, $lead->status);

        $component->call('previousStep')->call('nextStep');

        $lead->refresh();
        $this->assertSame(LeadStatus::Booked, $lead->status);
        $this->assertNotNull($lead->scheduled_at);

        Carbon::setTestNow();
    }

    public function test_book_without_lead_uuid_does_not_confirm(): void
    {
        $this->seedBookingSettings();

        $slots = app(BookingMatrix::class)->slots();
        $date = $slots[0]['date'];
        $time = $slots[0]['times'][0];

        Livewire::test(EstimatorWizard::class)
            ->call('book', $date, $time)
            ->assertHasErrors('booking')
            ->assertSet('booked', false);

        $this->assertSame(0, Lead::count());
    }

    private function makeProbeService(string $title, string $slug): Service
    {
        return Service::create([
            'title' => $title,
            'slug' => $slug,
            'category' => ServiceCategory::Care,
            'short_description' => 'Probe',
            'long_description' => 'Probe long',
            'icon' => 'sparkles',
            'is_active' => true,
            'base_price_multiplier' => 1.0,
            'sort_order' => 0,
        ]);
    }

    private function seedEstimatorBasics(): Service
    {
        Setting::set('service_areas', ['Kessler Park'], 'array');
        Setting::set('complexity_modifiers', [
            'simple' => 0.85,
            'standard' => 1.00,
            'complex' => 1.30,
        ], 'json', 'pricing');
        Setting::set('neighborhood_modifiers', ['Kessler Park' => 1.15], 'json', 'pricing');
        Setting::set('price_per_sqft_modifier', 3.25, 'decimal', 'pricing');

        return Service::factory()->create([
            'title' => 'Lawn Care',
            'slug' => 'lawn-care',
            'category' => ServiceCategory::Care,
            'short_description' => 'Weekly lawn maintenance',
            'long_description' => 'Full lawn care service.',
            'icon' => 'sparkles',
            'is_active' => true,
            'base_price_multiplier' => 1.0,
        ]);
    }

    private function seedBookingSettings(): void
    {
        Setting::set('booking_days_offered', 5, 'integer', 'operations');
        Setting::set('booking_time_slots', ['9:00 AM', '12:00 PM', '3:00 PM'], 'array', 'operations');
    }
}
