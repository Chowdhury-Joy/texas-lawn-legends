<?php

namespace App\Filament\Pages;

use App\Enums\ProductPart;
use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageProductParts extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Product Parts';

    protected static ?string $title = 'Product Parts';

    protected function settingsGroup(): string
    {
        return 'product';
    }

    public static function permissionKey(): string
    {
        return 'settings.product_parts';
    }

    public function mount(): void
    {
        parent::mount();

        if (blank($this->data['product_part'] ?? null)) {
            $this->form->fill([
                'product_part' => ProductPart::Ops->value,
            ]);
        }
    }

    protected function settingsMap(): array
    {
        return [
            'product_part' => 'integer',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('How much of the product is turned on?')
                ->description('Part 1 is the storefront. Part 2 adds booking. Part 3 adds the full ops workshop. Industry packs (lawn, cleaning, etc.) sit on top of whichever part you choose.')
                ->schema([
                    Radio::make('product_part')
                        ->label('Active product part')
                        ->options(collect(ProductPart::cases())->mapWithKeys(
                            fn (ProductPart $part) => [$part->value => $part->getLabel()]
                        )->all())
                        ->descriptions(collect(ProductPart::cases())->mapWithKeys(
                            fn (ProductPart $part) => [$part->value => $part->getDescription()]
                        )->all())
                        ->required()
                        ->default(ProductPart::Ops->value),
                ]),
        ];
    }
}
