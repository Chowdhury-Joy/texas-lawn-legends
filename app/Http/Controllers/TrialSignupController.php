<?php

namespace App\Http\Controllers;

use App\Services\TrialProvisioner;
use App\Support\Niche\NicheLoader;
use App\Support\Trial\TrialHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class TrialSignupController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $this->guardTrialHost();

        return view('trial.signup', [
            'durationDays' => TrialHost::durationDays(),
            'googleReady' => TrialHost::googleConfigured(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->guardTrialHost();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', Password::defaults()],
            'business_slug' => ['nullable', 'string', 'max:48', 'alpha_dash'],
        ]);

        $request->session()->put('trial_signup', [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'slug' => $data['business_slug'] ?? null,
            'google_id' => null,
        ]);

        return redirect()->route('trial.niche');
    }

    public function niche(): View|RedirectResponse
    {
        $this->guardTrialHost();

        if (! $this->hasSignupSession()) {
            return redirect()->route('trial.signup')
                ->withErrors(['email' => 'Create your account first, then pick an industry.']);
        }

        return view('trial.niche', [
            'cards' => NicheLoader::hubCards(),
            'durationDays' => TrialHost::durationDays(),
        ]);
    }

    public function provision(Request $request, TrialProvisioner $provisioner): RedirectResponse
    {
        $this->guardTrialHost();

        $signup = $request->session()->get('trial_signup');

        if (! is_array($signup) || blank($signup['email'] ?? null)) {
            return redirect()->route('trial.signup')
                ->withErrors(['email' => 'Create your account first, then pick an industry.']);
        }

        $data = $request->validate([
            'niche' => ['required', 'string', 'in:'.implode(',', array_keys(config('niche.packs', [])))],
        ]);

        try {
            [$user, $workspace] = $provisioner->provision(
                $data['niche'],
                [
                    'name' => (string) $signup['name'],
                    'email' => (string) $signup['email'],
                    'password' => $signup['password'] ?? null,
                    'google_id' => $signup['google_id'] ?? null,
                ],
                $signup['slug'] ?? null,
            );
        } catch (\InvalidArgumentException $exception) {
            return redirect()->route('trial.signup')
                ->withErrors(['email' => $exception->getMessage()]);
        }

        $request->session()->forget('trial_signup');

        Auth::login($user, remember: true);

        return redirect($workspace->publicPath('admin'));
    }

    private function guardTrialHost(): void
    {
        if (! TrialHost::enabled()) {
            abort(404);
        }
    }

    private function hasSignupSession(): bool
    {
        $signup = session('trial_signup');

        return is_array($signup) && filled($signup['email'] ?? null);
    }
}
