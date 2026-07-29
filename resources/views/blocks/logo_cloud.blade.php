@php
    $logos = (array) ($data['logos'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-t-4 border-slate-950 bg-slate-950">
    <div class="layout-container space-section">
        <div class="stack-header items-center text-center">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-emerald-900 px-2 py-0.5 type-tagline text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif
            @if (filled($data['heading'] ?? null))
                <h2 data-field="heading" data-reveal class="type-h3 text-white">
                    {{ $data['heading'] }}
                </h2>
            @endif
        </div>

        @if (! empty($logos))
            <div class="stack-after-header {{ $layout ?: 'flex flex-wrap items-center justify-center gap-container-xl' }}">
                @foreach ($logos as $index => $logo)
                    @php $url = filled($logo['image'] ?? null) ? public_url($logo['image']) : null; @endphp
                    <img data-field="logos.{{ $index }}.image" data-stagger style="--stagger-i: {{ $index }}" src="{{ $url ?: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="160" height="60" viewBox="0 0 160 60"><rect width="160" height="60" fill="%23334155" rx="4"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="14" font-weight="bold" fill="%2394a3b8">Logo</text></svg>' }}" alt="{{ $logo['label'] ?? '' }}" class="h-10 w-auto object-contain opacity-80 transition-opacity hover:opacity-100">
                @endforeach
            </div>
        @endif
    </div>
</section>
