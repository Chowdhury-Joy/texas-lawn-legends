@php
    $features = (array) ($data['features'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
    
@endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-7xl px-6 py-section">
        @if (filled($data['eyebrow'] ?? null))
            <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                {{ $data['eyebrow'] }}
            </span>
        @endif
        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" data-reveal class="mt-4 text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                {{ $data['heading'] }}
            </h2>
        @endif

        @if (! empty($features))
            <div class="mt-10 {{ $layout ?: 'grid grid-cols-1 gap-6 lg:grid-cols-3' }}">
                @foreach ($features as $index => $feature)
                    <div data-field="features.{{ $index }}" data-stagger style="--stagger-i: {{ $index }}" class="box-brutal p-4 sm:p-6">
                        @if (filled($feature['icon'] ?? null))
                            <x-svg-icon :name="$feature['icon']" class="h-8 w-8 text-emerald-900" />
                        @endif
                        @if (filled($feature['title'] ?? null))
                            <h3 data-field="features.{{ $index }}.title" class="mt-4 text-xl font-medium tracking-tighter text-slate-900">
                                {{ $feature['title'] }}
                            </h3>
                        @endif
                        @if (filled($feature['body'] ?? null))
                            <p data-field="features.{{ $index }}.body" class="mt-2 text-sm leading-relaxed text-slate-600">{{ $feature['body'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
