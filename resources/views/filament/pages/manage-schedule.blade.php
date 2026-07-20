<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Date Range Filter Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Schedule Overview</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">View and manage crew job allocations across Dallas project sites.</p>
            </div>
            <div class="flex items-center gap-2">
                @foreach ([
                    'this_week' => 'This Week',
                    'next_week' => 'Next Week',
                    'this_month' => 'This Month',
                    'all' => 'All Upcoming',
                ] as $key => $label)
                    <button type="button"
                            wire:click="$set('filterRange', '{{ $key }}')"
                            @class([
                                'px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors',
                                'bg-primary-600 text-white shadow-sm' => $filterRange === $key,
                                'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' => $filterRange !== $key,
                            ])>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Unassigned Projects Alert Box -->
        @if ($this->unassignedProjects->isNotEmpty())
            <div class="rounded-xl border-2 border-amber-400 bg-amber-50 p-4 dark:border-amber-500/50 dark:bg-amber-950/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white font-bold text-sm">!</span>
                        <div>
                            <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                                {{ $this->unassignedProjects->count() }} Unassigned Project(s) Need Crew Allocation
                            </h3>
                            <p class="text-xs text-amber-700 dark:text-amber-300">Assign a field crew to ensure smooth site operations and crew dispatch.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->unassignedProjects as $unassigned)
                        <div class="flex flex-col justify-between rounded-lg border border-amber-200 bg-white p-3 shadow-xs dark:border-amber-800 dark:bg-gray-900">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ $unassigned->neighborhood }}</span>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $unassigned->project_title }}</h4>
                                <p class="text-xs text-gray-500">{{ $unassigned->client_name }} · ${{ number_format($unassigned->contract_value) }}</p>
                                <p class="mt-1 text-[11px] font-medium text-gray-400">Start: {{ $unassigned->started_at?->format('M j, Y') ?? 'TBD' }}</p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Quick Assign Crew:</label>
                                <select wire:change="assignCrew({{ $unassigned->id }}, $event.target.value)" class="mt-1 w-full rounded-md border-gray-300 text-xs dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                    <option value="">Select Crew...</option>
                                    @foreach ($this->crews as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->leader_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Crew Schedule Board Columns -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($this->crewsWithProjects as $item)
                @php
                    $crew = $item['crew'];
                    $projects = $item['projects'];
                    $colorBadge = match ($crew->color) {
                        'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300',
                        'amber' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950 dark:text-amber-300',
                        'sky' => 'bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-950 dark:text-sky-300',
                        'purple' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-950 dark:text-purple-300',
                        'rose' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950 dark:text-rose-300',
                        'slate' => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
                        default => 'bg-gray-100 text-gray-800 border-gray-300 dark:bg-gray-800 dark:text-gray-300',
                    };
                @endphp
                <div class="rounded-xl border border-gray-200 bg-white shadow-xs dark:border-gray-800 dark:bg-gray-900 overflow-hidden flex flex-col">
                    <!-- Crew Header -->
                    <div class="border-b border-gray-100 p-4 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center justify-between">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border {{ $colorBadge }}">
                                {{ $crew->name }}
                            </span>
                            @if ($crew->leader_name)
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Foreman: <strong>{{ $crew->leader_name }}</strong> @if ($crew->phone) ({{ $crew->phone }}) @endif</p>
                            @endif
                        </div>
                        <span class="text-xs font-bold text-gray-400">{{ $projects->count() }} Job(s)</span>
                    </div>

                    <!-- Assigned Jobs List -->
                    <div class="p-4 space-y-3 flex-1">
                        @forelse ($projects as $pj)
                            <div class="rounded-lg border border-gray-200 bg-white p-3 shadow-xs dark:border-gray-800 dark:bg-gray-900 hover:border-gray-300">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">{{ $pj->neighborhood }}</span>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $pj->project_title }}</h4>
                                        <p class="text-xs text-gray-500">{{ $pj->client_name }}</p>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($pj->contract_value) }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                                    <span>📅 {{ $pj->started_at?->format('M j') ?? 'TBD' }} @if ($pj->completed_at) – {{ $pj->completed_at->format('M j') }} @endif</span>
                                    <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $pj]) }}" class="text-primary-600 hover:underline font-semibold">Edit Job →</a>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-gray-400 italic border border-dashed border-gray-200 rounded-lg dark:border-gray-800">
                                No jobs scheduled for this crew in the selected period.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-sm text-gray-500 bg-white rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                    No field crews created yet. Create crews under Operations → Field Crews to start scheduling!
                </div>
            @endforelse
        </div>

    </div>
</x-filament-panels::page>
