@php
    $logos = (array) ($data['logos'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-t-4 border-slate-950 bg-slate-950 px-6 py-12">
    <div class="mx-auto max-w-7xl">
        @if (filled($data['eyebrow'] ?? null))
            <span data-field="eyebrow" class="inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                {{ $data['eyebrow'] }}
            </span>
        @endif
        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" class="mt-3 text-center text-2xl font-medium tracking-tighter text-white sm:text-3xl">
                {{ $data['heading'] }}
            </h2>
        @endif

        @if (! empty($logos))
            <div class="{{ $layout ?: 'mt-8 flex flex-wrap items-center justify-center gap-8' }}">
                @foreach ($logos as $index => $logo)
                    @php $url = filled($logo['image'] ?? null) ? public_url($logo['image']) : null; @endphp
                    <img data-field="logos.{{ $index }}.image" src="{{ $url ?: 'https://via.placeholder.com/160x60.png?text=Logo' }}" alt="{{ $logo['label'] ?? '' }}" class="h-10 w-auto object-contain opacity-80 transition-opacity hover:opacity-100">
                @endforeach
            </div>
        @endif
    </div>
</section>
