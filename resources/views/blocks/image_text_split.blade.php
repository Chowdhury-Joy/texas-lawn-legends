@php
    $image = filled($data['image'] ?? null) ? \Illuminate\Support\Facades\Storage::disk('public')->url($data['image']) : null;
    $reverse = (bool) ($data['reverse'] ?? false);
@endphp
<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-16 lg:grid-cols-2">
        <div class="{{ $reverse ? 'lg:order-2' : '' }}">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $data['heading'] ?? '' }}" class="box-brutal aspect-[4/3] w-full object-cover">
            @endif
        </div>
        <div class="flex flex-col justify-center {{ $reverse ? 'lg:order-1' : '' }}">
            @if ($heading = $data['heading'] ?? null)
                <h2 class="text-3xl font-black uppercase tracking-tight text-slate-900 sm:text-4xl">{{ $heading }}</h2>
            @endif
            @if ($body = $data['body'] ?? null)
                <p class="mt-5 text-base leading-relaxed text-slate-600">{{ $body }}</p>
            @endif
        </div>
    </div>
</section>
