@php $images = (array) ($data['images'] ?? []); @endphp
@if (! empty($images))
    <section class="border-t-4 border-slate-950 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($images as $item)
                    @php $url = filled($item['image'] ?? null) ? \Illuminate\Support\Facades\Storage::disk('public')->url($item['image']) : null; @endphp
                    @if ($url)
                        <figure class="box-brutal overflow-hidden">
                            <img src="{{ $url }}" alt="{{ $item['caption'] ?? '' }}" class="aspect-square w-full object-cover">
                            @if ($caption = $item['caption'] ?? null)
                                <figcaption class="border-t-2 border-slate-950 p-3 text-sm text-slate-600">{{ $caption }}</figcaption>
                            @endif
                        </figure>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
