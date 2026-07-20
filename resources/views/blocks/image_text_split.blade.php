@php
    $image = filled($data['image'] ?? null) ? public_url($data['image']) : null;
    $reverse = (bool) ($data['reverse'] ?? false);
@endphp
<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-16 lg:grid-cols-2">
        <div class="{{ $reverse ? 'lg:order-2' : '' }}">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $data['heading'] ?? '' }}" loading="lazy" decoding="async" class="box-brutal aspect-[4/3] w-full object-cover">
            @endif
        </div>
        <div class="flex flex-col justify-center {{ $reverse ? 'lg:order-1' : '' }}">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" class="mb-3 inline-block w-fit bg-slate-950 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif
            @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                <h2 data-field="heading" class="text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                    {{ filled($data['heading'] ?? null) ? $data['heading'] : 'Precision Landscape Execution' }}
                </h2>
            @endif
            @if (filled($data['body'] ?? null))
                <p data-field="body" class="mt-5 text-base leading-relaxed text-slate-600">
                    {{ $data['body'] }}
                </p>
            @endif
        </div>
    </div>
</section>
