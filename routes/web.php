<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoHubController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProposalController;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/demo', [DemoHubController::class, 'index'])->name('demo.hub');
Route::post('/demo/load', [DemoHubController::class, 'load'])->name('demo.load');

/*
|--------------------------------------------------------------------------
| SEO utility routes
|--------------------------------------------------------------------------
*/

Route::get('/robots.txt', function () {
    $allowIndex = filter_var(setting('robots_index', true), FILTER_VALIDATE_BOOLEAN);

    $lines = $allowIndex
        ? ['User-agent: *', 'Allow: /', 'Disallow: /admin', '', 'Sitemap: '.url('/sitemap.xml')]
        : ['User-agent: *', 'Disallow: /'];

    return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
    ];

    if (product_part_at_least(2)) {
        $urls[] = ['loc' => url('/estimate'), 'priority' => '0.9', 'changefreq' => 'monthly'];
    }

    if (product_part_at_least(3)) {
        $urls[] = ['loc' => url('/portal'), 'priority' => '0.5', 'changefreq' => 'monthly'];
    }

    foreach (Page::query()->published()->whereNotNull('slug')->get() as $page) {
        $urls[] = ['loc' => url($page->slug), 'priority' => '0.5', 'changefreq' => 'monthly'];
    }

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin Live Reloading
|--------------------------------------------------------------------------
*/

Route::get('/api/site-version', function () {
    return response()->json(['version' => Cache::get('site_version', 1)]);
})->name('api.site-version');

/*
|--------------------------------------------------------------------------
| Product Part–gated public flows
|--------------------------------------------------------------------------
| Part 2 unlocks booking/estimate. Part 3 unlocks portal, dashboard,
| proposals, and invoices. CMS pages stay available at Part 1.
*/

Route::view('/estimate', 'estimate')
    ->middleware('product.part:2')
    ->name('estimate');

Route::view('/portal', 'portal')
    ->middleware('product.part:3')
    ->name('portal');

Route::get('/invoices/{invoice:unique_access_token}', [InvoiceController::class, 'show'])
    ->middleware(['product.part:3', 'throttle:60,1'])
    ->name('invoices.show');
Route::get('/dashboard/{project:unique_dashboard_hash}', [DashboardController::class, 'show'])
    ->middleware(['product.part:3', 'throttle:60,1'])
    ->name('dashboard');
Route::get('/proposals/{token}', [ProposalController::class, 'show'])
    ->middleware(['product.part:3', 'throttle:60,1'])
    ->name('proposals.show');
Route::post('/proposals/{token}/accept', [ProposalController::class, 'accept'])
    ->middleware(['product.part:3', 'throttle:60,1'])
    ->name('proposals.accept');
Route::post('/proposals/{token}/decline', [ProposalController::class, 'decline'])
    ->middleware(['product.part:3', 'throttle:60,1'])
    ->name('proposals.decline');

/*
|--------------------------------------------------------------------------
| CMS page catch-all
|--------------------------------------------------------------------------
| Must stay last — registration order decides precedence, and this matches
| any single path segment that wasn't claimed by a route above.
*/

Route::get('/{page:slug}', [PageController::class, 'show'])->name('page.show');
