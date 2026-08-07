<?php

namespace App\Filament\Resources\Equipment\Pages;

use App\Filament\Concerns\ExportsResourceData;
use App\Filament\Resources\Equipment\EquipmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEquipment extends ListRecords
{
    use ExportsResourceData;

    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return $this->mergeExportHeaderActions([
            CreateAction::make(),
        ]);
    }

    /**
     * @return array<string, callable(ListRecords): Builder|\Illuminate\Database\Query\Builder|null>
     */
    protected function exportTables(): array
    {
        return [
            'equipment' => fn (ListRecords $livewire) => $livewire->getTableQueryForExport(),
            'maintenance_logs' => fn (ListRecords $livewire) => $livewire->relatedTableQuery('maintenance_logs', 'equipment_id', 'equipment'),
        ];
    }
}
