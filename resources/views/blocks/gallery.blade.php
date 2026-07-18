@php $images = (array) ($data['images'] ?? []); @endphp
<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-16">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 tab:grid-cols-2 lg:grid-cols-3">
            @foreach ($images as $index => $item)
                @php $url = filled($item['image'] ?? null) ? \Illuminate\Support\Facades\Storage::disk('public')->url($item['image']) : null; @endphp
                <figure class="box-brutal overflow-hidden">
                    <img src="{{ $url ?: 'https://via.placeholder.com/400x400.png?text=Gallery+Image' }}" alt="{{ $item['caption'] ?? '' }}" loading="lazy" decoding="async" class="aspect-square w-full object-cover">
                    <figcaption data-field="images.{{ $index }}.caption" data-gjs-type="text" class="border-t-2 border-slate-950 p-3 text-sm text-slate-600">
                        {{ filled($item['caption'] ?? null) ? $item['caption'] : '[Gallery Caption]' }}
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
