@php
    $image = filled($data['image'] ?? null) ? public_url($data['image']) : null;
    $stats = (array) ($data['stats'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
    
@endphp
<section id="about" class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-6 py-16 lg:grid-cols-2 lg:items-center">
        <div class="{{ $data['reverse'] ?? false ? 'lg:order-2' : '' }}">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $data['heading'] ?? 'About Texas Lawn Legends' }}" loading="lazy" decoding="async" class="box-brutal aspect-[4/3] w-full object-cover">
            @endif
        </div>

        <div class="flex flex-col justify-center {{ $data['reverse'] ?? false ? 'lg:order-1' : '' }}">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" class="inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif

            @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                <h2 data-field="heading" class="mt-4 text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                    {{ filled($data['heading'] ?? null) ? $data['heading'] : 'About Texas Lawn Legends' }}
                </h2>
            @endif

            @if (filled($data['body'] ?? null))
                <p data-field="body" class="mt-5 text-base leading-snug tracking-tight text-slate-600">
                    {{ $data['body'] }}
                </p>
            @endif

            @if (! empty($stats))
                <div class="mt-8 {{ $layout ?: 'grid grid-cols-2 gap-4 sm:grid-cols-3' }}">
                    @foreach ($stats as $index => $stat)
                        @if (filled($stat['value'] ?? null) || filled($stat['label'] ?? null))
                            <div data-field="stats.{{ $index }}" class="box-brutal p-3 sm:p-4">
                                @if (filled($stat['value'] ?? null))
                                    <h3 data-field="stats.{{ $index }}.value" class="block text-4xl font-medium leading-none tracking-tighter text-emerald-900">
                                        {{ $stat['value'] }}
                                    </h3>
                                @endif
                                @if (filled($stat['label'] ?? null))
                                    <span data-field="stats.{{ $index }}.label" class="mt-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
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
