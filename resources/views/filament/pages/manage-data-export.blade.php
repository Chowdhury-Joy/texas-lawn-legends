@php
    use Illuminate\Support\Number;

    $summary = $this->summary();
    $counts = $summary['tables'];
    $uploads = $summary['uploads'];
    $allowed = $this->selfServeAllowed();
@endphp

<x-filament-panels::page>
    <div class="max-w-3xl space-y-6">
        @if ($allowed)
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-950 dark:border-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-100">
                <p class="font-semibold">Your data is yours — take a copy any time</p>
                <p class="mt-1 opacity-90">
                    Use <strong>Download export</strong> (top right) to build one ZIP file containing every business
                    record on this site as a CSV spreadsheet, plus every photo and file you have uploaded. No request,
                    no waiting on us. Good for backups, for your accountant, or for moving to another system.
                </p>
                <p class="mt-2 text-xs opacity-80">
                    Licence: <strong>{{ $this->track()->getLabel() }}</strong>
                </p>
            </div>
        @else
            <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100">
                <p class="font-semibold">Self-serve export is not enabled on this plan</p>
                <p class="mt-1 opacity-90">
                    This site runs on <strong>{{ $this->track()->getLabel() }}</strong> (subscription hosting). Your
                    records are still yours — contact Getwebfield and we will run the export for you. Buying the site
                    out to <strong>Track A — Own it</strong> turns this into a one-click download you control.
                </p>
                <p class="mt-2 text-xs opacity-80">
                    Everything listed below is what that export contains.
                </p>
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-white/5">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-white/10">
                <h2 class="text-sm font-semibold text-gray-950 dark:text-white">What the export contains</h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    One <code>data/&lt;table&gt;.csv</code> per list below, an <code>uploads/</code> folder with your
                    files, plus a README and manifest describing the layout.
                </p>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach ($this->tableGroups() as $group => $tables)
                    @php
                        $present = array_values(array_filter($tables, fn ($t) => array_key_exists($t, $counts)));
                    @endphp

                    @continue($present === [])

                    <div class="px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $group }}</p>
                        <dl class="mt-2 grid grid-cols-1 gap-x-6 gap-y-1 sm:grid-cols-2">
                            @foreach ($present as $table)
                                <div class="flex items-baseline justify-between gap-3 text-sm">
                                    <dt class="text-gray-700 dark:text-gray-200">{{ str($table)->replace('_', ' ')->title() }}</dt>
                                    <dd class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ number_format($counts[$table]) }} rows</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach

                <div class="px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Uploaded files</p>
                    <div class="mt-2 flex items-baseline justify-between gap-3 text-sm">
                        <span class="text-gray-700 dark:text-gray-200">Logos, favicons, progress photos, page images</span>
                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
                            {{ number_format($uploads['count']) }} files · {{ Number::fileSize($uploads['bytes'], precision: 1) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 px-4 py-3 text-xs text-gray-500 dark:border-white/10 dark:text-gray-400">
            <p class="font-semibold text-gray-700 dark:text-gray-200">Not included</p>
            <p class="mt-1">
                Staff password hashes and login tokens — those are credentials, not records, so everyone re-sets a
                password after a restore. Framework scratch tables (cache, queue jobs, sessions) are runtime state and
                are skipped too.
            </p>
        </div>
    </div>
</x-filament-panels::page>
