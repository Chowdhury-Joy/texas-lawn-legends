<?php

namespace App\Http\Controllers;

use App\Support\Niche\NicheLoader;
use App\Support\Niche\NicheResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DemoHubController extends Controller
{
    public function index(): View
    {
        $this->ensureHubEnabled();

        return view('demo.hub', [
            'cards' => NicheLoader::hubCards(),
            'activeId' => NicheResolver::activeId(),
        ]);
    }

    public function load(Request $request, NicheLoader $loader): RedirectResponse
    {
        $this->ensureHubEnabled();

        $niche = (string) $request->validate([
            'niche' => ['required', 'string', 'in:'.implode(',', array_keys(config('niche.packs', [])))],
        ])['niche'];

        $pack = $loader->load($niche, demoMode: true);

        return redirect('/')
            ->with('status', 'Loaded demo: '.$pack->label());
    }

    private function ensureHubEnabled(): void
    {
        if (! NicheResolver::demoHubEnabled()) {
            throw new NotFoundHttpException;
        }
    }
}
