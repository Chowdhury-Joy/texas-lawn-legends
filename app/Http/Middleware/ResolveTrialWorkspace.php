<?php

namespace App\Http\Middleware;

use App\Models\TrialWorkspace;
use App\Support\Trial\TrialHost;
use App\Support\Trial\TrialWorkspaceContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTrialWorkspace
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! TrialHost::enabled()) {
            abort(404);
        }

        $workspace = $this->resolveWorkspace($request);

        if ($workspace === null) {
            abort(404);
        }

        TrialWorkspaceContext::set($workspace);

        return $next($request);
    }

    private function resolveWorkspace(Request $request): ?TrialWorkspace
    {
        $parameter = $request->route('trialWorkspace');

        if ($parameter instanceof TrialWorkspace) {
            return $parameter;
        }

        if (is_string($parameter) && $parameter !== '') {
            return TrialWorkspace::query()->where('slug', $parameter)->first();
        }

        return null;
    }
}
