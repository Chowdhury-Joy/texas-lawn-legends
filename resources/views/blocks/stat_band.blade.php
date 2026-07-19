@php
    $stats = (array) ($data['stats'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
    
@endphp
<section class="border-y-4 border-slate-950 bg-emerald-900 px-6 py-14 text-white">
    <div class="mx-auto max-w-7xl">
        @if (filled($data['eyebrow'] ?? null))
            <span data-field="eyebrow" class="inline-block w-fit bg-slate-950 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                {{ $data['eyebrow'] }}
            </span>
        @endif

        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" class="mt-4 max-w-2xl text-3xl font-medium tracking-tighter text-white sm:text-4xl">
                {{ $data['heading'] }}
            </h2>
        @endif

        @if (! empty($stats))
            <div class="mt-10 {{ $layout ?: 'grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4' }}">
                @foreach ($stats as $index => $stat)
                    <div data-field="stats.{{ $index }}" class="border-2 border-yellow-400 p-4 sm:p-5">
                        <h3 data-field="stats.{{ $index }}.value" class="block text-4xl font-medium leading-none tracking-tighter text-yellow-400 sm:text-5xl">
                            {{ filled($stat['value'] ?? null) ? $stat['value'] : '[00]' }}
                        </h3>
                        <span data-field="stats.{{ $index }}.label" class="mt-2 block text-xs font-bold uppercase tracking-wide text-emerald-100">
                            {{ filled($stat['label'] ?? null) ? $stat['label'] : '[Label]' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
