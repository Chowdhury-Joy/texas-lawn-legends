<?php

namespace App\Filament\Resources\Proposals\Tables;

use App\Enums\ProposalStatus;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProposalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unique_token')
                    ->label('Token')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->limit(8),
                TextColumn::make('lead.name')
                    ->label('Lead')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.project_title')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('accepted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProposalStatus::class),
            ])
            ->recordActions([
                Action::make('view_public')
                    ->label('View Public')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => route('proposals.show', $record->unique_token))
                    ->openUrlInNewTab()
                    ->disabled(fn ($record) => $record->status === ProposalStatus::Draft)
                    ->tooltip(fn ($record) => $record->status === ProposalStatus::Draft
                        ? 'Set status to Sent before sharing the public link'
                        : null),
                Action::make('createProject')
                    ->label('Create project')
                    ->icon('heroicon-m-briefcase')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === ProposalStatus::Accepted
                        && $record->project()->doesntExist())
                    ->schema([
                        TextInput::make('client_name')
                            ->required()
                            ->default(fn ($record) => $record->lead?->name),
                        TextInput::make('project_title')
                            ->required()
                            ->default(fn ($record) => trim(($record->lead?->service_type ?? 'Lawn project').' — '.$record->lead?->name)),
                        TextInput::make('neighborhood')
                            ->required()
                            ->default(fn ($record) => $record->lead?->neighborhood),
                        TextInput::make('contract_value')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->helperText('Prefilled from the accepted proposal total — adjust if it changed.')
                            ->default(fn ($record) => $record->total_amount),
                    ])
                    ->action(function (array $data, $record) {
                        $project = Project::create([
                            'lead_id' => $record->lead_id,
                            'client_name' => $data['client_name'],
                            'project_title' => $data['project_title'],
                            'neighborhood' => $data['neighborhood'],
                            'contract_value' => $data['contract_value'],
                            'started_at' => now(),
                        ]);

                        $record->update(['project_id' => $project->id]);

                        Notification::make()
                            ->title('Project created from proposal')
                            ->success()
                            ->send();

                        return redirect(ProjectResource::getUrl('edit', ['record' => $project]));
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
