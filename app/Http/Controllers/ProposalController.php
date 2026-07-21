<?php

namespace App\Http\Controllers;

use App\Enums\ProposalStatus;
use App\Events\ProposalAccepted;
use App\Models\Proposal;

class ProposalController extends Controller
{
    public function show(string $token)
    {
        $proposal = Proposal::where('unique_token', $token)->firstOrFail();

        abort_if($proposal->status === ProposalStatus::Draft, 404);

        if ($proposal->expires_at && now()->isAfter($proposal->expires_at)) {
            abort(410, 'This proposal has expired.');
        }

        return view('proposals.show', [
            'proposal' => $proposal,
        ]);
    }

    public function accept(string $token)
    {
        $proposal = Proposal::where('unique_token', $token)->firstOrFail();

        abort_if($proposal->status === ProposalStatus::Draft, 404);

        if ($proposal->expires_at && now()->isAfter($proposal->expires_at)) {
            abort(410, 'This proposal has expired.');
        }

        // Idempotency guard — a double-click or retried request shouldn't
        // re-fire the operations alert for a proposal that's already accepted.
        if ($proposal->status !== ProposalStatus::Accepted) {
            $proposal->update([
                'status' => ProposalStatus::Accepted,
                'accepted_at' => now(),
            ]);

            ProposalAccepted::dispatch($proposal);
        }

        return redirect()->back()->with('success', 'Proposal accepted successfully!');
    }

    public function decline(string $token)
    {
        $proposal = Proposal::where('unique_token', $token)->firstOrFail();

        abort_if($proposal->status === ProposalStatus::Draft, 404);

        if ($proposal->expires_at && now()->isAfter($proposal->expires_at)) {
            abort(410, 'This proposal has expired.');
        }

        $proposal->update([
            'status' => ProposalStatus::Declined,
        ]);

        return redirect()->back()->with('success', 'Proposal declined.');
    }
}
