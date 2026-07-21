<?php

namespace App\Listeners;

use App\Events\ProposalAccepted;
use App\Services\OperationsNotifier;

class NotifyOperationsOfProposalAcceptance
{
    public function __construct(
        protected OperationsNotifier $notifier,
    ) {}

    public function handle(ProposalAccepted $event): void
    {
        $proposal = $event->proposal;

        $this->notifier->dispatch('Proposal accepted — ready to create project', [
            'proposal_id' => $proposal->id,
            'lead' => $proposal->lead?->name,
            'total_amount' => (float) $proposal->total_amount,
            'admin_url' => route('filament.admin.resources.proposals.edit', ['record' => $proposal->id]),
        ]);
    }
}
