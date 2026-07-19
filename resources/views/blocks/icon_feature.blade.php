@php
    $features = (array) ($data['features'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
    
@endphp
<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-16">
        @if (filled($data['eyebrow'] ?? null))
            <span data-field="eyebrow" class="inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                {{ $data['eyebrow'] }}
            </span>
        @endif
        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" class="mt-4 text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                {{ $data['heading'] }}
            </h2>
        @endif

        @if (! empty($features))
            <div class="mt-10 {{ $layout ?: 'grid grid-cols-1 gap-6 lg:grid-cols-3' }}">
                @foreach ($features as $index => $feature)
                    <div data-field="features.{{ $index }}" class="box-brutal p-4 sm:p-6">
                        @if (filled($feature['icon'] ?? null))
                            <x-svg-icon :name="$feature['icon']" class="h-8 w-8 text-emerald-900" />
                        @endif
                        <h3 data-field="features.{{ $index }}.title" class="mt-4 text-xl font-medium tracking-tighter text-slate-900">
                            {{ filled($feature['title'] ?? null) ? $feature['title'] : '[Feature]' }}
                        </h3>
                        @if (filled($feature['body'] ?? null))
                            <p data-field="features.{{ $index }}.body" class="mt-2 text-sm leading-relaxed text-slate-600">{{ $feature['body'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
