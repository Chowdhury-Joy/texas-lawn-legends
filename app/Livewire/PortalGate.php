<?php

namespace App\Livewire;

use App\Events\AddonOrdered;
use App\Models\Addon;
use App\Models\Lead;
use App\Models\Project;
use App\Services\MonthlyCodeAuthenticator;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PortalGate extends Component
{
    public string $code = '';

    public bool $unlocked = false;

    /** @var array<int, string> Titles of add-ons ordered this session */
    public array $ordered = [];

    public ?string $lastOrdered = null;

    public function mount(MonthlyCodeAuthenticator $auth): void
    {
        $this->unlocked = $auth->unlocked();
    }

    public function unlock(MonthlyCodeAuthenticator $auth): void
    {
        $this->validate([
            'code' => ['required', 'string', 'max:100'],
        ]);

        if (! $auth->attempt($this->code)) {
            $this->addError('code', 'That token is invalid or has expired for this month.');

            return;
        }

        $this->unlocked = true;
        $this->code = '';
    }

    public function lock(MonthlyCodeAuthenticator $auth): void
    {
        $auth->lock();
        $this->unlocked = false;
        $this->ordered = [];
        $this->lastOrdered = null;
    }

    public function order(int $addonId, MonthlyCodeAuthenticator $auth): void
    {
        // Re-verify the gate on every side-effecting action.
        if (! $auth->unlocked()) {
            $this->unlocked = false;

            return;
        }

        $addon = Addon::query()->available()->find($addonId);

        if (! $addon) {
            return;
        }

        AddonOrdered::dispatch($addon, $this->orderContext($auth));

        $this->ordered[] = $addon->title;
        $this->lastOrdered = $addon->title;
    }

    /**
     * Preserve the client/property context so operations can action the order.
     *
     * @return array<string, mixed>
     */
    protected function orderContext(MonthlyCodeAuthenticator $auth): array
    {
        $context = $auth->context() ?? [];
        $clientId = $context['client_id'] ?? null;

        $clientName = null;
        $neighborhood = null;

        if ($clientId) {
            $project = Project::find($clientId);
            $clientName = $project?->client_name;
            $neighborhood = $project?->neighborhood;

            if (! $clientName) {
                $lead = Lead::find($clientId);
                $clientName = $lead?->name;
                $neighborhood = $lead?->neighborhood;
            }
        }

        return array_filter([
            'access_code' => $context['code'] ?? null,
            'month' => $context['month'] ?? null,
            'client_id' => $clientId,
            'client_name' => $clientName,
            'neighborhood' => $neighborhood,
        ], fn ($v) => $v !== null);
    }

    #[Computed]
    public function addons()
    {
        return Addon::query()->available()->orderBy('title')->get();
    }

    public function render()
    {
        return view('livewire.portal-gate');
    }
}
