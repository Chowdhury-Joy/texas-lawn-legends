<?php

namespace App\Livewire;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Service;
use App\Services\BookingMatrix;
use App\Services\EstimatePricingEngine;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
    // Quick Quote mode: single service
    public ?int $service_id = null;

    // Full Estimate mode: multi-service cart
    public array $service_ids = [];

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
        $this->sqft = $this->sqftBounds['min'];
    }

    /**
     * Returns the active estimator mode: 'quick' or 'full'.
     */
    #[Computed]
    public function estimatorMode(): string
    {
        return (string) setting('estimator_mode', 'quick');
    }

    /**
     * @return array<string, mixed>
     */
    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => $this->estimatorMode === 'full'
                ? [
                    'service_ids'   => ['required', 'array', 'min:1'],
                    'service_ids.*' => ['integer', Rule::exists('services', 'id')->where('is_active', true)],
                    'neighborhood'  => ['required', 'string', Rule::in((array) setting('service_areas', []))],
                ]
                : [
                    'service_id'   => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
                    'neighborhood' => ['required', 'string', Rule::in((array) setting('service_areas', []))],
                ],
            2 => [
                'sqft'       => ['required', 'integer', 'min:'.(int) setting('estimate_min_sqft', 100), 'max:'.(int) setting('estimate_max_sqft', 10000)],
                'complexity' => ['required', Rule::in(array_keys((array) setting('complexity_modifiers', ['simple' => 0, 'standard' => 0, 'complex' => 0])))],
            ],
            3 => [
                'name'    => ['required', 'string', 'max:255'],
                'email'   => ['required', 'email', 'max:255'],
                'phone'   => ['required', 'regex:/^[\d\s\-\(\)\+\.]{7,20}$/'],
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
            'phone.regex'            => 'Please enter a valid phone number.',
            'neighborhood.in'        => 'We are not yet servicing that neighborhood — call us and we will see what we can do.',
            'service_ids.required'   => 'Please select at least one service to continue.',
            'service_ids.min'        => 'Please select at least one service to continue.',
        ];
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->step));

        // Resolved once and shared: computeEstimate() and persistLead() both need
        // the same rows, and a step advance should not query them twice.
        $services = $this->selectedServices();

        if ($this->step === 2) {
            $this->computeEstimate($services);
        }

        $this->persistLead($services);

        $this->step = min($this->step + 1, $this->totalSteps);
    }

    /**
     * Services backing the current selection, in either estimator mode.
     *
     * @return Collection<int, Service>
     */
    protected function selectedServices(): Collection
    {
        if ($this->estimatorMode === 'full') {
            return Service::query()->findMany($this->service_ids);
        }

        if (! $this->service_id) {
            return collect();
        }

        return Service::query()->findMany([$this->service_id]);
    }

    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function selectService(int $id): void
    {
        if ($this->estimatorMode === 'full') {
            // Toggle: add if not present, remove if already selected
            if (in_array($id, $this->service_ids, true)) {
                $this->service_ids = array_values(array_filter($this->service_ids, fn ($s) => $s !== $id));
            } else {
                $this->service_ids[] = $id;
            }
        } else {
            // Quick Quote: single select
            $this->service_id = $id;
        }
    }

    /**
     * @param  Collection<int, Service>|null  $services
     */
    protected function computeEstimate(?Collection $services = null): void
    {
        $engine = app(EstimatePricingEngine::class);
        $services ??= $this->selectedServices();

        if ($services->isEmpty()) {
            return;
        }

        $result = $this->estimatorMode === 'full'
            ? $engine->calculateMany($services, $this->sqft, $this->neighborhood, $this->complexity)
            : $engine->calculate($services->first(), $this->sqft, $this->neighborhood, $this->complexity);

        $this->estimateLow  = $result['low'];
        $this->estimateHigh = $result['high'];
        $this->isCustom     = $result['is_custom'];
    }

    /**
     * Persist / update the lead on every step forward so abandoned funnels are captured.
     *
     * @param  Collection<int, Service>|null  $services
     */
    protected function persistLead(?Collection $services = null): void
    {
        $services ??= $this->selectedServices();

        $serviceLabel = $services->isEmpty()
            ? null
            : $services->pluck('title')->join(', ');

        $status = match (true) {
            $this->booked => LeadStatus::Booked,
            $this->estimateLow !== null => LeadStatus::Qualified,
            default => LeadStatus::Partial,
        };

        if ($this->leadUuid) {
            $existing = Lead::query()->where('uuid', $this->leadUuid)->first();
            if ($existing?->status === LeadStatus::Booked) {
                $status = LeadStatus::Booked;
            }
        }

        $data = [
            'name'                     => $this->name ?: null,
            'email'                    => $this->email ?: null,
            'phone'                    => $this->phone ?: null,
            'address'                  => $this->address ?: null,
            'neighborhood'             => $this->neighborhood ?: null,
            'estimated_sqft'           => $this->sqft,
            'service_type'             => $serviceLabel,
            'calculated_estimate_low'  => $this->estimateLow,
            'calculated_estimate_high' => $this->estimateHigh,
            'step_reached'             => 'step_'.$this->step,
            'status'                   => $status,
            'referred_by_code'         => $this->referred_by_code,
            'is_demo'                  => \App\Support\Niche\NicheResolver::demoMode(),
        ];

        if ($this->leadUuid) {
            Lead::query()->where('uuid', $this->leadUuid)->update($data);
        } else {
            $this->leadUuid = Lead::create($data)->uuid;
        }
    }

    public function book(string $date, string $time): void
    {
        if (! $this->leadUuid) {
            $this->addError('booking', 'Please complete the estimate steps before booking.');

            return;
        }

        $matrix = app(BookingMatrix::class);

        if (! $matrix->isOfferedSlot($date, $time)) {
            $this->addError('booking', 'That time slot is not available. Please select another time.');

            return;
        }

        $parsedTime = Carbon::parse($date.' '.$time);

        $booked = DB::transaction(function () use ($parsedTime) {
            $isTaken = Lead::query()
                ->where('status', LeadStatus::Booked)
                ->where('scheduled_at', $parsedTime)
                ->lockForUpdate()
                ->exists();

            if ($isTaken) {
                return false;
            }

            if ($this->leadUuid) {
                Lead::query()->where('uuid', $this->leadUuid)->update([
                    'scheduled_at' => $parsedTime,
                    'status' => LeadStatus::Booked,
                    'step_reached' => 'booked',
                ]);
            }

            return true;
        });

        if (! $booked) {
            $this->addError('booking', 'Sorry, that time slot was just taken! Please select another time.');
            unset($this->bookingSlots);

            return;
        }

        $this->selectedDate = $date;
        $this->selectedTime = $time;
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

    #[Computed]
    public function sqftStep(): int
    {
        $range = max(1, $this->sqftBounds['max'] - $this->sqftBounds['min']);

        return min(50, max(1, (int) round($range / 15)));
    }

    /**
     * Live estimated range calculation preview for Step 2 interaction.
     *
     * @return array{low: float, high: float, is_custom: bool}|null
     */
    #[Computed]
    public function liveEstimatePreview(): ?array
    {
        $engine = app(EstimatePricingEngine::class);
        $fallbackNeighborhood = $this->neighborhood ?: (string) collect($this->neighborhoods)->first();

        if ($this->estimatorMode === 'full') {
            if (empty($this->service_ids)) {
                return null;
            }

            $services = Service::query()->findMany($this->service_ids);

            if ($services->isEmpty()) {
                return null;
            }

            return $engine->calculateMany($services, $this->sqft, $fallbackNeighborhood, $this->complexity);
        }

        if (! $this->service_id) {
            return null;
        }

        $service = Service::find($this->service_id);

        if (! $service) {
            return null;
        }

        return $engine->calculate($service, $this->sqft, $fallbackNeighborhood, $this->complexity);
    }

    public function render()
    {
        return view('livewire.estimator-wizard');
    }
}
