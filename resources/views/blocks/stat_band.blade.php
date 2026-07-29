@php
    $stats = (array) ($data['stats'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-y-4 border-slate-950 bg-emerald-900 text-on-primary">
    <div class="layout-container space-section">
        <div class="stack-header">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-slate-950 px-2 py-0.5 type-tagline text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif

            @if (filled($data['heading'] ?? null))
                <h2 data-field="heading" data-reveal class="type-h2 max-w-2xl text-on-primary">
                    {{ $data['heading'] }}
                </h2>
            @endif
        </div>

        @if (! empty($stats))
            <div class="stack-after-header {{ $layout ?: 'grid-stats' }}">
                @foreach ($stats as $index => $stat)
                    @if (filled($stat['value'] ?? null) || filled($stat['label'] ?? null))
                        <div data-field="stats.{{ $index }}" data-stagger style="--stagger-i: {{ $index }}" class="border-2 border-yellow-400 space-card">
                            @if (filled($stat['value'] ?? null))
                                <h3 data-field="stats.{{ $index }}.value" class="type-h2 text-yellow-400">
                                    {{ $stat['value'] }}
                                </h3>
                            @endif
                            @if (filled($stat['label'] ?? null))
                                <span data-field="stats.{{ $index }}.label" class="type-tagline mt-[var(--space-sm)] block text-emerald-100">
                                    {{ $stat['label'] }}
                                </span>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
