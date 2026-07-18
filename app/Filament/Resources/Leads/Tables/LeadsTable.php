<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Lead;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->email),
                TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                TextColumn::make('neighborhood')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('service_type')
                    ->toggleable(),
                TextColumn::make('calculated_estimate_low')
                    ->money('usd')
                    ->label('Est. Low')
                    ->sortable(),
                TextColumn::make('calculated_estimate_high')
                    ->money('usd')
                    ->label('Est. High')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(LeadStatus::class),
                SelectFilter::make('neighborhood')
                    ->options(fn () => Lead::query()
                        ->whereNotNull('neighborhood')
                        ->distinct()
                        ->pluck('neighborhood', 'neighborhood')
                        ->toArray()),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')->label('Created from'),
                        DatePicker::make('created_until')->label('Created until'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['created_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['created_until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'From '.$data['created_from'];
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Until '.$data['created_until'];
                        }

                        return $indicators;
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('createProject')
                    ->label('Create project')
                    ->icon('heroicon-m-briefcase')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, [LeadStatus::Qualified, LeadStatus::Booked], true)
                        && $record->project()->doesntExist())
                    ->schema([
                        TextInput::make('client_name')
                            ->required()
                            ->default(fn ($record) => $record->name),
                        TextInput::make('project_title')
                            ->required()
                            ->default(fn ($record) => trim(($record->service_type ?? 'Lawn project').' — '.$record->name)),
                        TextInput::make('neighborhood')
                            ->required()
                            ->default(fn ($record) => $record->neighborhood),
                        TextInput::make('contract_value')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->helperText('Prefilled from the high estimate — adjust to the signed amount.')
                            ->default(fn ($record) => $record->calculated_estimate_high),
                    ])
                    ->action(function (array $data, $record) {
                        $project = Project::create([
                            'lead_id' => $record->id,
                            'client_name' => $data['client_name'],
                            'project_title' => $data['project_title'],
                            'neighborhood' => $data['neighborhood'],
                            'contract_value' => $data['contract_value'],
                            'started_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Project created from lead')
                            ->success()
                            ->send();

                        return redirect(ProjectResource::getUrl('edit', ['record' => $project]));
                    }),
                EditAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markContacted')
                        ->label('Mark as contacted')
                        ->icon('heroicon-m-phone')
                        ->color('info')
                        ->action(fn (Collection $records) => static::setStatus($records, LeadStatus::Contacted))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('markBooked')
                        ->label('Mark as booked')
                        ->icon('heroicon-m-calendar-days')
                        ->color('success')
                        ->action(fn (Collection $records) => static::setStatus($records, LeadStatus::Booked))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('markLost')
                        ->label('Mark as lost')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => static::setStatus($records, LeadStatus::Lost))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function setStatus(Collection $records, LeadStatus $status): void
    {
        $records->each(fn ($record) => $record->update(['status' => $status]));

        Notification::make()
            ->title('Updated '.$records->count().' lead(s) to '.$status->getLabel())
            ->success()
            ->send();
    }
}
