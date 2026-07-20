<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

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
                    ->label('Issued')
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(InvoiceStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('print')
                    ->label('View / Print')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('gray')
                    ->url(fn (Invoice $record): string => route('invoices.show', $record->unique_access_token))
                    ->openUrlInNewTab(),

                Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Invoice $record): bool => $record->status !== InvoiceStatus::Paid && $record->status !== InvoiceStatus::Cancelled)
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        $record->update(['status' => InvoiceStatus::Paid]);
                        Notification::make()
                            ->title("Invoice {$record->invoice_number} marked as Paid")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
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
