<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
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

    protected string $view = 'filament.pages.settings-form';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function getPageRecord(): PageModel
    {
        return PageModel::home();
    }

    public function mount(): void
    {
        $this->form->fill([
            'blocks' => $this->getPageRecord()->blocks ?? [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Builder::make('blocks')
                    ->label('Homepage Sections')
                    ->blocks([
                        Builder\Block::make('hero')
                            ->label('Hero')
                            ->schema(PageBlocks::fields('hero')),
                        Builder\Block::make('trust_bar')
                            ->label('Trust Bar')
                            ->schema(PageBlocks::fields('trust_bar')),
                        Builder\Block::make('three_step')
                            ->label('3-Step Process')
                            ->schema(PageBlocks::fields('three_step')),
                        Builder\Block::make('service_matrix')
                            ->label('Service Matrix')
                            ->schema(PageBlocks::fields('service_matrix')),
                        Builder\Block::make('neighborhood_proof')
                            ->label('Neighborhood Proof')
                            ->schema(PageBlocks::fields('neighborhood_proof')),
                        Builder\Block::make('cta_banner')
                            ->label('CTA Banner')
                            ->schema(PageBlocks::fields('cta_banner')),
                        Builder\Block::make('rich_text')
                            ->label('Rich Text')
                            ->schema(PageBlocks::fields('rich_text')),
                        Builder\Block::make('image_text_split')
                            ->label('Image + Text Split')
                            ->schema(PageBlocks::fields('image_text_split')),
                        Builder\Block::make('gallery')
                            ->label('Gallery')
                            ->schema(PageBlocks::fields('gallery')),
                        Builder\Block::make('faq')
                            ->label('FAQ')
                            ->schema(PageBlocks::fields('faq')),
                        Builder\Block::make('about')
                            ->label('About Us')
                            ->schema(PageBlocks::fields('about')),
                    ])
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
