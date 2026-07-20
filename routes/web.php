<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PageController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

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
        ['loc' => url('/estimate'), 'priority' => '0.9', 'changefreq' => 'monthly'],
        ['loc' => url('/portal'), 'priority' => '0.5', 'changefreq' => 'monthly'],
    ];

    foreach (Page::query()->published()->whereNotNull('slug')->get() as $page) {
        $urls[] = ['loc' => url($page->slug), 'priority' => '0.5', 'changefreq' => 'monthly'];
    }

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Placeholder routes — fleshed out in later phases
|--------------------------------------------------------------------------
| These keep site-wide navigation coherent while the Livewire estimator,
| member portal, and client dashboard are built out.
*/

Route::view('/estimate', 'estimate')->name('estimate');

Route::view('/portal', 'portal')->name('portal');

Route::get('/invoices/{invoice:unique_access_token}', [InvoiceController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('invoices.show');
Route::get('/dashboard/{project:unique_dashboard_hash}', [DashboardController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| CMS page catch-all
|--------------------------------------------------------------------------
| Must stay last — registration order decides precedence, and this matches
| any single path segment that wasn't claimed by a route above.
*/

Route::get('/{page:slug}', [PageController::class, 'show'])->name('page.show');
