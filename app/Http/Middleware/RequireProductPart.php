<?php

namespace App\Http\Middleware;

use App\Enums\ProductPart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireProductPart
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, int|string $minimum): Response
    {
        $required = ProductPart::tryFrom((int) $minimum) ?? ProductPart::Ops;

        abort_unless(product_part()->atLeast($required), 404);

        return $next($request);
    }
}
