<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Services\OperationsNotifier;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('project_title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->client_name),
                TextColumn::make('neighborhood')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('contract_value')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('profit_margin_percent')
                    ->label('Margin %')
                    ->formatStateUsing(fn ($state) => $state === null ? 'N/A' : $state.'%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state >= 40 => 'success',
                        $state >= 20 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('calculated_profit_margin', $direction)),
                TextColumn::make('crew.name')
                    ->label('Crew')
                    ->placeholder('Unassigned')
                    ->badge()
                    ->color(fn ($record) => match ($record->crew?->color) {
                        'emerald' => 'success',
                        'amber' => 'warning',
                        'sky' => 'info',
                        'purple' => 'primary',
                        'rose' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('milestones_count')
                    ->counts('milestones')
                    ->label('Milestones')
                    ->badge()
                    ->color('info'),
                TextColumn::make('started_at')
                    ->date('M j, Y')
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->date('M j, Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProjectStatus::class),
                SelectFilter::make('crew_id')
                    ->relationship('crew', 'name')
                    ->label('Crew'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('sendReviewRequest')
                    ->label(fn ($record) => $record->review_requested_at ? 'Resend Review Request' : 'Request Review')
                    ->icon('heroicon-o-star')
                    ->color(fn ($record) => $record->review_requested_at ? 'gray' : 'warning')
                    ->visible(fn ($record) => $record->status === ProjectStatus::Completed)
                    ->requiresConfirmation()
                    ->modalHeading('Send Google Review Request')
                    ->modalDescription('Generates a review request notification for this client.')
                    ->action(function ($record) {
                        $placeId = setting('google_place_id');

                        if (empty($placeId)) {
                            Notification::make()
                                ->title('Google Place ID missing')
                                ->body('Please configure the Google Place ID in Settings.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $link = "https://search.google.com/local/writereview?placeid={$placeId}";

                        app(OperationsNotifier::class)->dispatch('Review Request', [
                            'client_name' => $record->client_name,
                            'project_title' => $record->project_title,
                            'review_link' => $link,
                        ]);

                        $record->update(['review_requested_at' => now()]);

                        Notification::make()
                            ->title("Review request sent to {$record->client_name}")
                            ->success()
                            ->send();
                    }),
                Action::make('viewDashboard')
                    ->label('View')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn ($record) => route('dashboard', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
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
