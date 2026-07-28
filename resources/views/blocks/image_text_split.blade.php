@php
    $image = filled($data['image'] ?? null) ? public_url($data['image']) : null;
    $reverse = (bool) ($data['reverse'] ?? false);
@endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div @class(['grid-split mx-auto max-w-7xl space-section', 'is-reversed' => $reverse])>
        <div data-reveal>
            @if ($image)
                <img src="{{ $image }}" alt="{{ $data['heading'] ?? '' }}" loading="lazy" decoding="async" class="box-brutal aspect-[4/3] w-full object-cover">
            @endif
        </div>
        <div class="stack-copy justify-center">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-slate-950 px-2 py-0.5 type-tagline text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif
            @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                <h2 data-field="heading" data-reveal class="type-h2 text-slate-900">
                    {{ filled($data['heading'] ?? null) ? $data['heading'] : 'Precision Landscape Execution' }}
                </h2>
            @endif
            @if (filled($data['body'] ?? null))
                <p data-field="body" data-reveal class="type-body-lg text-slate-600">
                    {{ $data['body'] }}
                </p>
            @endif
        </div>
    </div>
</section>
