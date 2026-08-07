@php
    $image = filled($data['image'] ?? null) ? public_url($data['image']) : null;
    $stats = (array) ($data['stats'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
    $reversed = (bool) ($data['reverse'] ?? false);
@endphp
<section id="about" class="border-t-4 border-slate-950 bg-brand-paper">
    <div @class(['grid-split layout-container space-section', 'is-reversed' => $reversed])>
        <div data-reveal>
            @if ($image)
                <img src="{{ $image }}" alt="{{ $data['heading'] ?? ('About '.(setting('site_name') ?: 'Us')) }}" loading="lazy" decoding="async" class="box-brutal aspect-[4/3] w-full object-cover">
            @endif
        </div>

        <div class="stack-copy justify-center">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-emerald-900 px-2 py-0.5 type-tagline text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif

            @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                <h2 data-field="heading" data-reveal class="type-h2 text-slate-900">
                    {{ filled($data['heading'] ?? null) ? $data['heading'] : ('About '.(setting('site_name') ?: 'Us')) }}
                </h2>
            @endif

            @if (filled($data['body'] ?? null))
                <p data-field="body" data-reveal class="type-body-lg text-slate-600">
                    {{ $data['body'] }}
                </p>
            @endif

            @if (! empty($stats))
                <div class="stack-after-header {{ $layout ?: 'grid-stats-sm' }}">
                    @foreach ($stats as $index => $stat)
                        @if (filled($stat['value'] ?? null) || filled($stat['label'] ?? null))
                            <div data-field="stats.{{ $index }}" data-stagger style="--stagger-i: {{ $index }}" class="box-brutal space-card">
                                @if (filled($stat['value'] ?? null))
                                    <h3 data-field="stats.{{ $index }}.value" class="type-h3 text-emerald-900">
                                        {{ $stat['value'] }}
                                    </h3>
                                @endif
                                @if (filled($stat['label'] ?? null))
                                    <span data-field="stats.{{ $index }}.label" class="type-tagline mt-1 block text-slate-500">
                                        {{ $stat['label'] }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
