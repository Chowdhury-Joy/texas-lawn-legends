@php
    $reviews = $testimonials ?? collect();
    if (! empty($data['random'])) {
        $review = $reviews->isNotEmpty() ? $reviews->random() : null;
    } else {
        $review = $reviews->where('is_featured', true)->first() ?: $reviews->first();
    }
@endphp
<section class="border-t-4 border-slate-950 bg-emerald-900 px-6 py-20 text-white">
    <div class="mx-auto max-w-3xl text-center">
        @if (filled($data['eyebrow'] ?? null))
            <span data-field="eyebrow" class="inline-block w-fit bg-slate-950 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                {{ $data['eyebrow'] }}
            </span>
        @endif
        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" class="mt-4 text-3xl font-medium tracking-tighter sm:text-4xl">
                {{ $data['heading'] }}
            </h2>
        @endif

        @if ($review)
            <figure class="mt-10 box-brutal bg-white p-6 text-slate-900 sm:p-10">
                <div class="text-2xl font-black leading-none text-yellow-400" style="-webkit-text-stroke: 2px #0f172a;">“</div>
                <blockquote class="mt-2 text-xl font-medium leading-snug tracking-tighter sm:text-2xl">“{{ $review->review_text }}”</blockquote>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <div class="text-yellow-500">{!! str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating) !!}</div>
                    <figcaption class="text-left">
                        <span class="block text-sm font-bold uppercase tracking-wide text-slate-900">{{ $review->author }}</span>
                        @if (filled($review->neighborhood))
                            <span class="block text-xs font-medium text-emerald-800">{{ $review->neighborhood }}</span>
                        @endif
                    </figcaption>
                </div>
                <p class="mt-4 inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-widest text-emerald-800">
                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Verified Local Review
                </p>
            </figure>
        @else
            <p class="mt-10 text-center text-emerald-100/80">A featured review will appear here soon.</p>
        @endif
    </div>
</section>
