<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageHomepage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Homepage Content';

    protected static ?string $title = 'Homepage Content';

    protected string $view = 'filament.pages.manage-homepage';

    public function getPageRecord(): PageModel
    {
        return PageModel::home();
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
}
