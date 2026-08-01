<?php

namespace App\Filament\Concerns;

use App\Services\DataExportService;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Adds a scoped CSV/ZIP export to a resource list page.
 *
 * The export respects the page's active tab, filters, search, and sort —
 * staff get exactly the rows they are looking at, not a silent full-table dump.
 * Gated to Track A like the full export; permission comes from the resource
 * itself (if you can open the list, you can export it).
 */
trait ExportsResourceData
{
    /**
     * Tables to export. Key = table name. Value = resolver that receives the
     * list page and returns a filtered query. Defaults to the resource model's
     * table filtered through the list page.
     *
     * @return array<string, (callable(ListRecords): EloquentBuilder|QueryBuilder)|null>
     */
    protected function exportTables(): array
    {
        $table = static::getResource()::getModel()::make()->getTable();

        return [
            $table => fn (ListRecords $livewire): EloquentBuilder => $livewire->getTableQueryForExport(),
        ];
    }

    /**
     * Stem used in the downloaded filename (e.g. "invoices").
     */
    protected function exportFilenameStem(): string
    {
        return Str::slug(static::getResource()::getPluralModelLabel());
    }

    /**
     * @param  array<int, Action>  $actions
     * @return array<int, Action>
     */
    protected function mergeExportHeaderActions(array $actions): array
    {
        $actions[] = $this->makeExportAction();

        return $actions;
    }

    protected function makeExportAction(): Action
    {
        $tables = $this->exportTables();

        return Action::make('export')
            ->label(count($tables) > 1 ? 'Export (.zip)' : 'Export CSV')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->visible(fn (): bool => DataExportService::selfServeAllowed())
            ->action(function (DataExportService $export): BinaryFileResponse {
                $queries = [];

                foreach ($this->exportTables() as $table => $resolver) {
                    $queries[$table] = $resolver($this);
                }

                $path = $export->generateScoped(
                    $this->exportFilenameStem(),
                    $queries,
                    auth()->user(),
                );

                return response()
                    ->download($path, basename($path))
                    ->deleteFileAfterSend();
            });
    }

    /**
     * Child rows for a parent table export (e.g. invoice_items for invoices).
     */
    protected function relatedTableQuery(string $table, string $foreignKey, string $parentTable): QueryBuilder
    {
        $parentResolver = $this->exportTables()[$parentTable] ?? null;

        if ($parentResolver === null) {
            return DB::table($table)->whereRaw('1 = 0');
        }

        /** @var EloquentBuilder $parentQuery */
        $parentQuery = $parentResolver($this);

        return DB::table($table)
            ->whereIn($foreignKey, $parentQuery->select($parentTable.'.id'))
            ->orderBy('id');
    }
}
