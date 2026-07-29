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
