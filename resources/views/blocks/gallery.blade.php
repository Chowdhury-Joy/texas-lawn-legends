@php
    $images = (array) ($data['images'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-16">
        <div class="{{ $layout ?: 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3' }}">
            @foreach ($images as $index => $item)
                @php $url = filled($item['image'] ?? null) ? public_url($item['image']) : null; @endphp
                <figure class="box-brutal overflow-hidden">
                    <img src="{{ $url ?: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect width="400" height="400" fill="%23e2e8f0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" font-weight="bold" fill="%2364748b">Gallery Image</text></svg>' }}" alt="{{ $item['caption'] ?? '' }}" loading="lazy" decoding="async" class="aspect-square w-full object-cover">
                    @if (filled($item['caption'] ?? null))
                        <figcaption data-field="images.{{ $index }}.caption" class="border-t-2 border-slate-950 p-3 text-sm text-slate-600">
                            {{ $item['caption'] }}
                        </figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
