<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project.project_title')
                    ->label('Project')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('issue_date')
                    ->label('Invoice Date')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Due')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total ($)')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->defaultSort('issue_date', 'desc')
            ->filtersFormColumns(3)
            ->filters([
                // From / To / Status sit on one row: the date range spans two of
                // the three filter-form columns, leaving the third for status.
                Filter::make('issue_date')
                    ->label('Invoice Date')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        DatePicker::make('issued_from')
                            ->label('From date')
                            ->native(false)
                            ->maxDate(fn (Get $get) => $get('issued_until')),
                        DatePicker::make('issued_until')
                            ->label('To date')
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('issued_from')),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['issued_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('issue_date', '>=', $date))
                        ->when($data['issued_until'] ?? null, fn (Builder $q, $date) => $q->whereDate('issue_date', '<=', $date)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['issued_from'] ?? null) {
                            $indicators[] = 'From '.$data['issued_from'];
                        }
                        if ($data['issued_until'] ?? null) {
                            $indicators[] = 'Until '.$data['issued_until'];
                        }

                        return $indicators;
                    }),

                SelectFilter::make('status')
                    ->options(InvoiceStatus::class),

                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('print')
                    ->label('View / Print')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('gray')
                    ->button()
                    ->outlined()
                    ->size(Size::Small)
                    ->url(fn (Invoice $record): string => route('invoices.show', $record->unique_access_token))
                    ->openUrlInNewTab(),

                Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->button()
                    ->outlined()
                    ->size(Size::Small)
                    ->visible(fn (Invoice $record): bool => $record->status !== InvoiceStatus::Paid && $record->status !== InvoiceStatus::Cancelled)
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        $record->update(['status' => InvoiceStatus::Paid]);
                        Notification::make()
                            ->title("Invoice {$record->invoice_number} marked as Paid")
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->button()
                    ->outlined()
                    ->size(Size::Small),
                DeleteAction::make()
                    ->button()
                    ->outlined()
                    ->size(Size::Small),
                RestoreAction::make()
                    ->button()
                    ->outlined()
                    ->size(Size::Small),
                ForceDeleteAction::make()
                    ->button()
                    ->outlined()
                    ->size(Size::Small),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
