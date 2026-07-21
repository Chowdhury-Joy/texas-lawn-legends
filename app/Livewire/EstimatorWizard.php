<?php

namespace App\Livewire;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Service;
use App\Services\BookingMatrix;
use App\Services\EstimatePricingEngine;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class EstimatorWizard extends Component
{
    public int $step = 1;

    public int $totalSteps = 4;

    #[Url(as: 'ref')]
    public ?string $referred_by_code = null;

    // Step 1 — contact + neighborhood
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    #[Url]
    public string $neighborhood = '';

    // Step 2 — service scope
    public ?int $service_id = null;

    // Step 3 — dimensions + complexity
    public int $sqft = 500;

    public string $complexity = 'standard';

    // Results
    public ?float $estimateLow = null;

    public ?float $estimateHigh = null;

    public bool $isCustom = false;

    // Booking
    public ?string $selectedDate = null;

    public ?string $selectedTime = null;

    public bool $booked = false;

    public ?string $leadUuid = null;

    public function mount(): void
    {
        $this->sqft = max((int) setting('estimate_min_sqft', 100), 500);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
                'neighborhood' => ['required', 'string', Rule::in((array) setting('service_areas', []))],
            ],
            2 => [
                'sqft' => ['required', 'integer', 'min:'.(int) setting('estimate_min_sqft', 100), 'max:'.(int) setting('estimate_max_sqft', 10000)],
                'complexity' => ['required', Rule::in(array_keys((array) setting('complexity_modifiers', ['simple' => 0, 'standard' => 0, 'complex' => 0])))],
            ],
            3 => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required', 'regex:/^[\d\s\-\(\)\+\.]{7,20}$/'],
                'address' => ['nullable', 'string', 'max:255'],
            ],
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number.',
            'neighborhood.in' => 'We are not yet servicing that neighborhood — call us and we will see what we can do.',
        ];
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->step));

        if ($this->step === 2) {
            $this->computeEstimate();
        }

        $this->persistLead();

        $this->step = min($this->step + 1, $this->totalSteps);
    }

    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function selectService(int $id): void
    {
        $this->service_id = $id;
    }

    protected function computeEstimate(): void
    {
        $service = Service::find($this->service_id);

        if (! $service) {
            return;
        }

        $result = app(EstimatePricingEngine::class)->calculate(
            $service,
            $this->sqft,
            $this->neighborhood,
            $this->complexity,
        );

        $this->estimateLow = $result['low'];
        $this->estimateHigh = $result['high'];
        $this->isCustom = $result['is_custom'];
    }

    /**
     * Persist / update the lead on every step forward so abandoned funnels are captured.
     */
    protected function persistLead(): void
    {
        $service = $this->service_id ? Service::find($this->service_id) : null;

        $data = [
            'name' => $this->name ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
            'neighborhood' => $this->neighborhood ?: null,
            'estimated_sqft' => $this->sqft,
            'service_type' => $service?->title,
            'calculated_estimate_low' => $this->estimateLow,
            'calculated_estimate_high' => $this->estimateHigh,
            'step_reached' => 'step_'.$this->step,
            'status' => $this->estimateLow !== null ? LeadStatus::Qualified : LeadStatus::Partial,
            'referred_by_code' => $this->referred_by_code,
        ];

        if ($this->leadUuid) {
            Lead::query()->where('uuid', $this->leadUuid)->update($data);
        } else {
            $this->leadUuid = Lead::create($data)->uuid;
        }
    }

    public function book(string $date, string $time): void
    {
        $this->selectedDate = $date;
        $this->selectedTime = $time;

        if ($this->leadUuid) {
            Lead::query()->where('uuid', $this->leadUuid)->update([
                'scheduled_at' => Carbon::parse($date.' '.$time),
                'status' => LeadStatus::Booked,
                'step_reached' => 'booked',
            ]);
        }

        $this->booked = true;
    }

    #[Computed]
    public function services()
    {
        return Service::query()->active()->ordered()->get();
    }

    /**
     * @return array<int, array{date: string, label: string, times: array<int, string>}>
     */
    #[Computed]
    public function bookingSlots(): array
    {
        return app(BookingMatrix::class)->slots();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function neighborhoods(): array
    {
        return array_values((array) setting('service_areas', []));
    }

    /**
     * @return array{min: int, max: int}
     */
    #[Computed]
    public function sqftBounds(): array
    {
        return [
            'min' => (int) setting('estimate_min_sqft', 100),
            'max' => (int) setting('estimate_max_sqft', 10000),
        ];
    }

    /**
     * Live estimated range calculation preview for Step 3 interaction.
     *
     * @return array{low: float, high: float, is_custom: bool}|null
     */
    #[Computed]
    public function liveEstimatePreview(): ?array
    {
        if (! $this->service_id) {
            return null;
        }

        $service = Service::find($this->service_id);

        if (! $service) {
            return null;
        }

        return app(EstimatePricingEngine::class)->calculate(
            $service,
            $this->sqft,
            $this->neighborhood ?: (string) collect($this->neighborhoods)->first(),
            $this->complexity,
        );
    }

    public function render()
    {
        return view('livewire.estimator-wizard');
    }
}
