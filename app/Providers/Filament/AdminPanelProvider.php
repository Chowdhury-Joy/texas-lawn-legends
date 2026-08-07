<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Widgets\BusinessSnapshot;
use App\Filament\Widgets\LeadConversionStats;
use App\Filament\Widgets\LeadFunnel;
use App\Filament\Widgets\NeedsAttention;
use App\Filament\Widgets\RecentActivity;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\UpcomingSiteVisits;
use App\Filament\Widgets\WeeklyLeadTrend;
use App\Http\Middleware\ResolveTrialWorkspace;
use App\Support\Trial\TrialHost;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->brandName(fn () => setting('site_name') ?: config('app.name'))
            ->favicon(fn () => niche_favicon())
            ->login(Login::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->topbar(false)
            ->maxContentWidth(Width::Full)
            ->databaseNotifications()
            ->colors([
                'primary' => array_replace(Color::hex('#2173BD'), [
                    400 => '#3d8fd4',
                    500 => '#2e82c9',
                    600 => '#2173BD',
                    700 => '#1a5f9e',
                ]),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->navigationGroups([
                'Operations',
                'Site Content',
                'Configuration',
                'Site Settings',
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                BusinessSnapshot::class,
                NeedsAttention::class,
                UpcomingSiteVisits::class,
                LeadConversionStats::class,
                RevenueChart::class,
                LeadFunnel::class,
                WeeklyLeadTrend::class,
                RecentActivity::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->bootUsing(function (): void {
                Table::configureUsing(function (Table $table): void {
                    $table->modifyUngroupedRecordActionsUsing(
                        fn (Action $action): Action => $action->button()->outlined(),
                    );
                });
            });

        if (TrialHost::enabled()) {
            return $panel
                ->path('trial/{trialWorkspace}/admin')
                ->middleware([
                    ResolveTrialWorkspace::class,
                ], isPersistent: true);
        }

        return $panel->path('admin');
    }
}
