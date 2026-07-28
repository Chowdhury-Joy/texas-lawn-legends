@php
    $features = (array) ($data['features'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-7xl space-section">
        <div class="stack-header">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-emerald-900 px-2 py-0.5 type-tagline text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif
            @if (filled($data['heading'] ?? null))
                <h2 data-field="heading" data-reveal class="type-h2 text-slate-900">
                    {{ $data['heading'] }}
                </h2>
            @endif
        </div>

        @if (! empty($features))
            <div class="stack-after-header {{ $layout ?: 'grid-services' }}">
                @foreach ($features as $index => $feature)
                    <div data-field="features.{{ $index }}" data-stagger style="--stagger-i: {{ $index }}" class="box-brutal space-card">
                        @if (filled($feature['icon'] ?? null))
                            <x-svg-icon :name="$feature['icon']" class="h-8 w-8 text-emerald-900" />
                        @endif
                        @if (filled($feature['title'] ?? null))
                            <h3 data-field="features.{{ $index }}.title" class="type-h4 mt-[var(--space-md)] text-slate-900">
                                {{ $feature['title'] }}
                            </h3>
                        @endif
                        @if (filled($feature['body'] ?? null))
                            <p data-field="features.{{ $index }}.body" class="type-body-md mt-[var(--space-sm)] text-slate-600">{{ $feature['body'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
