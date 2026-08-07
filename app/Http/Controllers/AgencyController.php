<?php

namespace App\Http\Controllers;

use App\Support\Trial\TrialHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AgencyController extends Controller
{
    public function home(): View|RedirectResponse
    {
        if (! TrialHost::enabled()) {
            abort(404);
        }

        return view('agency.home', [
            'durationDays' => TrialHost::durationDays(),
            'googleReady' => TrialHost::googleConfigured(),
        ]);
    }
}
