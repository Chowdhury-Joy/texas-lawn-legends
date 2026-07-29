<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Support\Niche\NicheLoader;
use App\Support\Niche\NicheResolver;
use App\Support\ProductFeatures;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageIndustryPacks extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Industry Packs';

    protected static ?string $title = 'Industry Packs / Demo';

    protected string $view = 'filament.pages.manage-industry-packs';

    /**
     * @var array{niche: string}|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();
        $key = static::permissionKey();

        return ($user?->canAccessKey($key) ?? false) && ProductFeatures::allows($key);
    }

    public static function permissionKey(): string
    {
        return 'settings.industry_packs';
    }

    public function mount(): void
    {
        $this->form->fill([
            'niche' => NicheResolver::activeId(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $options = [];
        $descriptions = [];

        foreach (config('niche.packs', []) as $id => $class) {
            $pack = app($class);
            $options[$id] = $pack->label();
            $descriptions[$id] = $pack->hubBlurb();
        }

        return $schema
            ->components([
                Section::make('Which industry starter kit is loaded?')
                    ->description('Product Parts control Website / Booking / Ops. This page swaps the industry skin and demo starter content. Load or Restore model home after a sales call — only on demo installs (APP_DEMO_HUB).')
                    ->schema([
                        Radio::make('niche')
                            ->label('Industry pack')
                            ->options($options)
                            ->descriptions($descriptions)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('load')
                ->label('Load this pack')
                ->color('warning')
                ->visible(fn (): bool => NicheResolver::demoHubEnabled())
                ->requiresConfirmation()
                ->modalHeading('Load industry pack?')
                ->modalDescription('Restores this niche\'s model home — branding, pages, services, sample project, and ops demo data. Pitch edits from the last meeting are cleared.')
                ->action(function (NicheLoader $loader): void {
                    $niche = (string) ($this->form->getState()['niche'] ?? NicheResolver::activeId());
                    $pack = $loader->load($niche, demoMode: true);

                    Notification::make()
                        ->title('Loaded '.$pack->label())
                        ->body('Demo mode is on. Open the public site or /demo hub to walk the pitch.')
                        ->success()
                        ->send();
                }),
            Action::make('reset')
                ->label('Restore model home')
                ->color('gray')
                ->visible(fn (): bool => NicheResolver::demoHubEnabled())
                ->requiresConfirmation()
                ->modalHeading('Restore model home?')
                ->modalDescription('Reloads the currently active pack so the next meeting in this niche starts from the clean showroom — not the last prospect\'s edits.')
                ->action(function (NicheLoader $loader): void {
                    $pack = $loader->reset();
                    $this->form->fill(['niche' => $pack->id()]);

                    Notification::make()
                        ->title('Restored '.$pack->label())
                        ->success()
                        ->send();
                }),
        ];
    }
}
