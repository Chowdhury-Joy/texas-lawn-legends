<?php

namespace App\Http\Controllers;

use App\Support\Trial\TrialHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! TrialHost::enabled()) {
            abort(404);
        }

        if (! TrialHost::googleConfigured()) {
            return redirect()->route('trial.signup')
                ->withErrors(['email' => 'Google sign-in is not configured on this host yet.']);
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! TrialHost::enabled() || ! TrialHost::googleConfigured()) {
            abort(404);
        }

        $googleUser = Socialite::driver('google')->user();

        $request->session()->put('trial_signup', [
            'name' => $googleUser->getName() ?: 'Trial Owner',
            'email' => Str::lower((string) $googleUser->getEmail()),
            'password' => null,
            'slug' => null,
            'google_id' => (string) $googleUser->getId(),
        ]);

        return redirect()->route('trial.niche');
    }
}
