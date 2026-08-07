<?php

use App\Http\Controllers\AgencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoHubController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\TrialSignupController;
use App\Http\Controllers\TrialWorkspaceController;
use App\Http\Middleware\ResolveTrialWorkspace;
use App\Models\Page;
use App\Support\SiteVersion;
use App\Support\Trial\TrialHost;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (TrialHost::showsAgencyHome()) {
        return app(AgencyController::class)->home();
    }

    return app(PageController::class)->home();
})->name('home');

Route::get('/agency', [AgencyController::class, 'home'])->name('agency.home');

/*
|--------------------------------------------------------------------------
| V3 trial signup (APP_TRIAL_HOST)
|--------------------------------------------------------------------------
*/

Route::get('/trial/signup', [TrialSignupController::class, 'show'])->name('trial.signup');
Route::post('/trial/signup', [TrialSignupController::class, 'store'])->name('trial.signup.store');
Route::get('/trial/niche', [TrialSignupController::class, 'niche'])->name('trial.niche');
Route::post('/trial/niche', [TrialSignupController::class, 'provision'])->name('trial.niche.store');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::get('/demo', [DemoHubController::class, 'index'])->name('demo.hub');
Route::post('/demo/load', [DemoHubController::class, 'load'])->name('demo.load');
Route::post('/demo/reset', [DemoHubController::class, 'reset'])->name('demo.reset');

/*
|--------------------------------------------------------------------------
| SEO utility routes
|--------------------------------------------------------------------------
*/

Route::get('/robots.txt', function () {
    $allowIndex = filter_var(setting('robots_index', true), FILTER_VALIDATE_BOOLEAN);

    $lines = $allowIndex
        ? ['User-agent: *', 'Allow: /', 'Disallow: /admin', 'Disallow: /trial/*/admin', '', 'Sitemap: '.url('/sitemap.xml')]
        : ['User-agent: *', 'Disallow: /'];

    return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
    ];

    if (! TrialHost::enabled() && product_part_at_least(2)) {
        $urls[] = ['loc' => url('/estimate'), 'priority' => '0.9', 'changefreq' => 'monthly'];
    }

    if (! TrialHost::enabled() && product_part_at_least(3)) {
        $urls[] = ['loc' => url('/portal'), 'priority' => '0.5', 'changefreq' => 'monthly'];
    }

    if (! TrialHost::enabled()) {
        foreach (Page::query()->published()->whereNotNull('slug')->get() as $page) {
            $urls[] = ['loc' => url($page->slug), 'priority' => '0.5', 'changefreq' => 'monthly'];
        }
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
    return response()->json(['version' => SiteVersion::current()]);
})->name('api.site-version');

/*
|--------------------------------------------------------------------------
| V3 trial workspace public routes (APP_TRIAL_HOST)
|--------------------------------------------------------------------------
*/

Route::prefix('trial/{trialWorkspace}')
    ->middleware(ResolveTrialWorkspace::class)
    ->group(function (): void {
        Route::get('/', [TrialWorkspaceController::class, 'home'])->name('trial.workspace.home');

        Route::view('/estimate', 'estimate')
            ->middleware('product.part:2')
            ->name('trial.workspace.estimate');

        Route::view('/portal', 'portal')
            ->middleware('product.part:3')
            ->name('trial.workspace.portal');

        Route::get('/invoices/{invoice:unique_access_token}', [InvoiceController::class, 'show'])
            ->middleware(['product.part:3', 'throttle:60,1'])
            ->name('trial.workspace.invoices.show');

        Route::get('/dashboard/{project:unique_dashboard_hash}', [DashboardController::class, 'show'])
            ->middleware(['product.part:3', 'throttle:60,1'])
            ->name('trial.workspace.dashboard');

        Route::get('/proposals/{token}', [ProposalController::class, 'show'])
            ->middleware(['product.part:3', 'throttle:60,1'])
            ->name('trial.workspace.proposals.show');

        Route::post('/proposals/{token}/accept', [ProposalController::class, 'accept'])
            ->middleware(['product.part:3', 'throttle:60,1'])
            ->name('trial.workspace.proposals.accept');

        Route::post('/proposals/{token}/decline', [ProposalController::class, 'decline'])
            ->middleware(['product.part:3', 'throttle:60,1'])
            ->name('trial.workspace.proposals.decline');

        Route::get('/{page:slug}', [TrialWorkspaceController::class, 'show'])
            ->name('trial.workspace.page');
    });

/*
|--------------------------------------------------------------------------
| Product Part–gated public flows (non-trial installs)
|--------------------------------------------------------------------------
*/

if (! config('trial.enabled')) {
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

    Route::get('/{page:slug}', [PageController::class, 'show'])->name('page.show');
}
