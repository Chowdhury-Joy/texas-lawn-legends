<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Presets for the ranges staff ask for daily. "All" stays first so it is the
     * default active tab — a quiet day would otherwise open on an empty table
     * and read as broken. The From/To filter below handles any other range.
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn (): int => Invoice::query()->count()),

            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('issue_date', today()))
                ->badge(fn (): int => Invoice::query()->whereDate('issue_date', today())->count()),

            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('issue_date', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString(),
                ]))
                ->badge(fn (): int => Invoice::query()->whereBetween('issue_date', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString(),
                ])->count()),

            'this_month' => Tab::make('This Month')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('issue_date', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ]))
                ->badge(fn (): int => Invoice::query()->whereBetween('issue_date', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])->count()),

            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', InvoiceStatus::Overdue))
                ->badge(fn (): int => Invoice::query()->where('status', InvoiceStatus::Overdue)->count())
                ->badgeColor('danger'),
        ];
    }
}
