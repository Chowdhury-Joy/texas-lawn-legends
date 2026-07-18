<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| SEO utility routes
|--------------------------------------------------------------------------
*/

Route::get('/robots.txt', function () {
    $allowIndex = filter_var(setting('robots_index', true), FILTER_VALIDATE_BOOLEAN);

    $lines = $allowIndex
        ? ["User-agent: *", "Allow: /", "Disallow: /admin", "", 'Sitemap: '.url('/sitemap.xml')]
        : ["User-agent: *", "Disallow: /"];

    return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => url('/estimate'), 'priority' => '0.9', 'changefreq' => 'monthly'],
        ['loc' => url('/portal'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['loc' => url('/privacy'), 'priority' => '0.3', 'changefreq' => 'yearly'],
    ];

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

Route::view('/privacy', 'placeholder', [
    'heading' => 'Privacy Compliance Terms',
    'body' => 'Our privacy and compliance documentation will live here.',
])->name('privacy');

Route::get('/dashboard/{project:unique_dashboard_hash}', [DashboardController::class, 'show'])->name('dashboard');
