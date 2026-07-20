<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
use App\Models\User;
use App\Support\PageBlocks;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageHomepage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Homepage Content';

    protected static ?string $title = 'Homepage Content';

    protected string $view = 'filament.pages.manage-homepage';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user?->canAccessKey('settings.homepage') ?? false;
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function getPageRecord(): PageModel
    {
        // Admin path: this record is saved to, so it must exist.
        return PageModel::homeOrCreate();
    }

    public function mount(): void
    {
        $blocks = $this->getPageRecord()->blocks ?? [];

        $this->form->fill([
            'blocks' => PageBlocks::seedTextElementsForBlocks($blocks),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Builder::make('blocks')
                    ->label('Homepage Sections')
                    ->blocks(PageBlocks::builderBlocks())
                    ->collapsible()
                    ->collapsed()
                    ->cloneable()
                    ->blockNumbers(false)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->getPageRecord()->update([
            'blocks' => $data['blocks'] ?? [],
        ]);

        Notification::make()
            ->title('Homepage content saved')
            ->success()
            ->send();
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewSite')
                ->label('View site')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url('/')
                ->openUrlInNewTab(),
        ];
    }

    /**
     * @return array<int, Action>
     */
    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }
}
