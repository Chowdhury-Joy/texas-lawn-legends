<?php

namespace App\Services;

use App\Enums\LicenseTrack;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

/**
 * X-01 — full self-serve data export.
 *
 * Packages every business record the install owns into one ZIP the client
 * admin can download without asking Getwebfield: one RFC-4180 CSV per table
 * under `data/`, every uploaded file under `uploads/`, plus `manifest.json`
 * and a plain-text `README.txt` explaining the layout.
 *
 * CSV (not a SQL dump) because the point of the export is portability — the
 * owner can open it in Excel/Sheets, import it into another CRM, or hand it
 * to a developer. Uploads ride along in the same archive so photo/logo paths
 * stored in the CSVs still resolve after a restore.
 *
 * Gated to Track A installs (see App\Enums\LicenseTrack); Track B exports
 * stay a Getwebfield super-admin operation until a buy-out.
 */
class DataExportService
{
    /** Bump when the archive layout changes in a way importers must notice. */
    public const FORMAT_VERSION = 1;

    /** Tables written to `data/*.csv`, grouped the way the README reads. */
    public const TABLE_GROUPS = [
        'Sales & customers' => ['leads', 'proposals', 'invoices', 'invoice_items'],
        'Jobs & field ops' => ['projects', 'milestones', 'progress_photos', 'crews', 'time_entries', 'equipment', 'maintenance_logs'],
        'Website & catalog' => ['pages', 'services', 'testimonials', 'addons', 'access_codes'],
        'Configuration' => ['settings', 'users', 'permissions'],
        'Audit trail' => ['activity_log'],
    ];

    /**
     * Columns stripped from the export. Password hashes and remember tokens
     * are credentials, not business records — staff re-set passwords after a
     * restore rather than carrying hashes around inside a downloaded ZIP.
     *
     * @var array<string, array<int, string>>
     */
    private const REDACTED_COLUMNS = [
        'users' => ['password', 'remember_token'],
    ];

    /** Relative directory (on the local disk) where archives are built. */
    private const BUILD_DIR = 'exports';

    public static function selfServeAllowed(): bool
    {
        return LicenseTrack::current()->allowsSelfServeExport();
    }

    /**
     * Flat list of exported tables, skipping any this install has not migrated.
     *
     * @return array<int, string>
     */
    public static function tables(): array
    {
        $tables = [];

        foreach (self::TABLE_GROUPS as $group) {
            foreach ($group as $table) {
                if (Schema::hasTable($table)) {
                    $tables[] = $table;
                }
            }
        }

        return $tables;
    }

    /**
     * What the next export would contain — row counts per table plus upload
     * totals. Rendered on the admin page so the download is never a surprise.
     *
     * @return array{tables: array<string, int>, uploads: array{count: int, bytes: int}}
     */
    public function summary(): array
    {
        $tables = [];

        foreach (self::tables() as $table) {
            $tables[$table] = DB::table($table)->count();
        }

        return [
            'tables' => $tables,
            'uploads' => $this->uploadTotals(),
        ];
    }

    /**
     * Build the archive and return its absolute path. The caller streams it
     * and deletes it; nothing is left sitting in storage.
     *
     * @throws RuntimeException when the install is not licensed for self-serve export
     */
    public function generate(?User $actor = null): string
    {
        if (! self::selfServeAllowed()) {
            throw new RuntimeException('Self-serve data export is not available on this licence track.');
        }

        $generatedAt = Carbon::now();
        $path = $this->buildPath($generatedAt);

        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create the export archive at '.$path);
        }

        $counts = [];

        foreach (self::tables() as $table) {
            [$csv, $rows] = $this->csvFor($table);
            $zip->addFromString("data/{$table}.csv", $csv);
            $counts[$table] = $rows;
        }

        $uploads = $this->addUploads($zip);

        $manifest = [
            'format' => 'getwebfield-data-export',
            'version' => self::FORMAT_VERSION,
            'generated_at' => $generatedAt->toIso8601String(),
            'generated_by' => $actor ? ['name' => $actor->name, 'email' => $actor->email] : null,
            'site' => [
                'name' => $this->siteName(),
                'url' => config('app.url'),
                'niche' => niche()->id(),
                'product_part' => product_part()->value,
                'licence_track' => LicenseTrack::current()->value,
            ],
            'tables' => $counts,
            'uploads' => $uploads,
            'redacted_columns' => self::REDACTED_COLUMNS,
        ];

        $zip->addFromString('manifest.json', (string) json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $zip->addFromString('README.txt', $this->readme($manifest));

        $zip->close();

        activity()
            ->causedBy($actor)
            ->withProperties([
                'tables' => count($counts),
                'rows' => array_sum($counts),
                'uploads' => $uploads['count'],
                'file' => basename($path),
            ])
            ->log('Downloaded a full data export');

        return $path;
    }

    /**
     * Export one or more tables from a resource list page. A single table
     * downloads as CSV; multiple tables (e.g. invoices + line items) bundle
     * into a small ZIP. Queries should come from the list page's active
     * filters, search, and tab so the file matches what the user is looking at.
     *
     * @param  array<string, EloquentBuilder|QueryBuilder>  $queries  table name => filtered query
     *
     * @throws RuntimeException when the install is not licensed for self-serve export
     */
    public function generateScoped(string $scope, array $queries, ?User $actor = null): string
    {
        if (! self::selfServeAllowed()) {
            throw new RuntimeException('Self-serve data export is not available on this licence track.');
        }

        if ($queries === []) {
            throw new RuntimeException('Nothing to export.');
        }

        $generatedAt = Carbon::now();

        if (count($queries) === 1) {
            $table = array_key_first($queries);
            [$csv, $rows] = $this->csvFor($table, $queries[$table]);
            $path = $this->buildScopedPath($scope, $generatedAt, 'csv');
            file_put_contents($path, $csv);

            $this->logScopedExport($scope, [$table => $rows], basename($path), $actor);

            return $path;
        }

        $counts = [];
        $csvs = [];

        foreach ($queries as $table => $query) {
            [$csv, $rows] = $this->csvFor($table, $query);
            $counts[$table] = $rows;
            $csvs[$table] = $csv;
        }

        $path = $this->buildScopedPath($scope, $generatedAt, 'zip');
        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create the export archive at '.$path);
        }

        foreach ($csvs as $table => $csv) {
            $zip->addFromString("{$table}.csv", $csv);
        }

        $zip->addFromString('README.txt', $this->scopedReadme($scope, $generatedAt, $counts));
        $zip->close();

        $this->logScopedExport($scope, $counts, basename($path), $actor);

        return $path;
    }

    /**
     * One table as an RFC-4180 CSV string, plus its row count. Rows stream in
     * chunks and buffer to a temp stream, so a long leads table does not have
     * to sit in memory as one concatenated string.
     *
     * @return array{0: string, 1: int}
     */
    private function csvFor(string $table, EloquentBuilder|QueryBuilder|null $query = null): array
    {
        $columns = array_values(array_diff(
            Schema::getColumnListing($table),
            self::REDACTED_COLUMNS[$table] ?? []
        ));

        $handle = fopen('php://temp/maxmemory:'.(2 * 1024 * 1024), 'r+');
        fputcsv($handle, $columns, ',', '"', '');

        if ($query === null) {
            $query = DB::table($table);

            if (in_array('id', Schema::getColumnListing($table), true)) {
                $query->orderBy('id');
            }
        } elseif ($query instanceof EloquentBuilder && $query->getQuery()->orders === null) {
            $query->orderBy($table.'.id');
        } elseif ($query instanceof QueryBuilder && $query->orders === null && in_array('id', $columns, true)) {
            $query->orderBy('id');
        }

        $rows = 0;

        foreach ($query->lazy() as $record) {
            $values = [];
            $row = $record instanceof Model
                ? $record->getAttributes()
                : (array) $record;

            foreach ($columns as $column) {
                $values[] = $this->stringify($row[$column] ?? null);
            }

            fputcsv($handle, $values, ',', '"', '');
            $rows++;
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return [$csv, $rows];
    }

    /** Scalar-safe cell value: nulls blank, booleans 0/1, everything else cast. */
    private function stringify(mixed $value): string
    {
        return match (true) {
            $value === null => '',
            is_bool($value) => $value ? '1' : '0',
            is_array($value) => (string) json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Copy every uploaded file (logos, favicons, progress photos, block
     * images) into `uploads/`, mirroring its path on the public disk.
     *
     * @return array{count: int, bytes: int}
     */
    private function addUploads(ZipArchive $zip): array
    {
        $disk = Storage::disk('public');
        $count = 0;
        $bytes = 0;

        foreach ($disk->allFiles() as $file) {
            if ($this->isHousekeepingFile($file)) {
                continue;
            }

            $absolute = $disk->path($file);

            if (! is_readable($absolute)) {
                continue;
            }

            $zip->addFile($absolute, 'uploads/'.$file);
            $count++;
            $bytes += (int) $disk->size($file);
        }

        if ($count === 0) {
            $zip->addFromString('uploads/.gitkeep', '');
        }

        return ['count' => $count, 'bytes' => $bytes];
    }

    /**
     * Dotfiles Laravel keeps on the public disk (`.gitignore`, `.DS_Store`)
     * are plumbing, not the client's uploads — they only make the archive
     * look like it holds files the owner never put there.
     */
    private function isHousekeepingFile(string $path): bool
    {
        return str_starts_with(basename($path), '.');
    }

    /**
     * @return array{count: int, bytes: int}
     */
    private function uploadTotals(): array
    {
        $disk = Storage::disk('public');
        $count = 0;
        $bytes = 0;

        foreach ($disk->allFiles() as $file) {
            if ($this->isHousekeepingFile($file)) {
                continue;
            }

            $count++;
            $bytes += (int) $disk->size($file);
        }

        return ['count' => $count, 'bytes' => $bytes];
    }

    /**
     * The business's own name, not the framework's. `app.name` is install
     * boilerplate ("Laravel") on sites whose branding lives in settings, and
     * the owner should recognise the file they just downloaded.
     */
    private function siteName(): string
    {
        return (string) (setting('site_name') ?: config('app.name', 'site'));
    }

    private function buildPath(Carbon $generatedAt): string
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory(self::BUILD_DIR);

        $name = (Str::slug($this->siteName()) ?: 'site').'-data-export-'
            .$generatedAt->format('Y-m-d-His').'-'.Str::lower(Str::random(6)).'.zip';

        return $disk->path(self::BUILD_DIR.'/'.$name);
    }

    private function buildScopedPath(string $scope, Carbon $generatedAt, string $extension): string
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory(self::BUILD_DIR);

        $name = (Str::slug($this->siteName()) ?: 'site').'-'.Str::slug($scope).'-export-'
            .$generatedAt->format('Y-m-d-His').'.'.$extension;

        return $disk->path(self::BUILD_DIR.'/'.$name);
    }

    /**
     * @param  array<string, int>  $counts
     */
    private function logScopedExport(string $scope, array $counts, string $file, ?User $actor): void
    {
        activity()
            ->causedBy($actor)
            ->withProperties([
                'scope' => $scope,
                'tables' => $counts,
                'rows' => array_sum($counts),
                'file' => $file,
            ])
            ->log('Exported '.$scope.' data');
    }

    /**
     * @param  array<string, int>  $counts
     */
    private function scopedReadme(string $scope, Carbon $generatedAt, array $counts): string
    {
        $lines = [
            $this->siteName().' — '.$scope.' export',
            'Generated: '.$generatedAt->toIso8601String(),
            '',
            'FILES',
        ];

        foreach ($counts as $table => $rows) {
            $lines[] = sprintf('  %-20s %s rows', $table.'.csv', $rows);
        }

        $lines[] = '';
        $lines[] = 'This export reflects the filters, search, and tab active on the list page when you clicked Export.';

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $manifest
     */
    private function readme(array $manifest): string
    {
        $lines = [
            $this->siteName().' — full data export',
            'Generated: '.$manifest['generated_at'],
            'Format version: '.self::FORMAT_VERSION,
            '',
            'WHAT IS IN HERE',
            '  data/*.csv    One file per table. First row is the column header.',
            '  uploads/      Every uploaded file, in the same folder layout the',
            '                site uses. Paths stored in the CSVs (logos, progress',
            '                photos, page-block images) resolve against this folder.',
            '  manifest.json Machine-readable index: row counts, upload totals,',
            '                site settings snapshot, and what was redacted.',
            '',
            'TABLES',
        ];

        foreach (self::TABLE_GROUPS as $group => $tables) {
            $present = array_values(array_filter($tables, fn (string $t) => isset($manifest['tables'][$t])));

            if ($present === []) {
                continue;
            }

            $lines[] = '  '.$group;

            foreach ($present as $table) {
                $lines[] = sprintf('    %-20s %s rows', $table, $manifest['tables'][$table]);
            }
        }

        $lines = array_merge($lines, [
            '',
            'NOT INCLUDED',
            '  Staff password hashes and remember tokens. Everyone re-sets their',
            '  password after a restore. Nothing else about your business is held back.',
            '  Framework scratch tables (cache, jobs, sessions) are runtime state,',
            '  not records, so they are skipped too.',
            '',
            'HANDLE WITH CARE',
            '  This archive contains customer names, addresses, phone numbers, and',
            '  billing history. Store it somewhere private.',
            '',
        ]);

        return implode("\n", $lines);
    }
}
