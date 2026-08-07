@php
    $reviews = $testimonials ?? collect();
    if (! empty($data['random'])) {
        $review = $reviews->isNotEmpty() ? $reviews->random() : null;
    } else {
        $review = $reviews->where('is_featured', true)->first() ?: $reviews->first();
    }
@endphp
<section class="border-t-4 border-slate-950 bg-emerald-900 space-section text-on-primary">
    <div class="mx-auto max-w-3xl text-center">
        @if (filled($data['eyebrow'] ?? null))
            <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-slate-950 px-2 py-0.5 type-tagline text-yellow-400">
                {{ $data['eyebrow'] }}
            </span>
        @endif
        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" data-reveal class="type-h2 mt-[var(--space-md)] text-on-primary">
                {{ $data['heading'] }}
            </h2>
        @endif

        @if ($review)
            <figure data-reveal class="box-brutal stack-after-header space-card bg-white text-slate-900">
                <div class="type-h2 leading-none text-yellow-400" style="-webkit-text-stroke: 2px #0f172a;">“</div>
                <blockquote class="type-h3 mt-[var(--space-sm)]">“{{ $review->review_text }}”</blockquote>
                <div class="mt-[var(--space-lg)] flex items-center justify-center gap-container-sm">
                    @php $stars = max(0, min(5, (int) $review->rating)); @endphp
                    <div class="text-yellow-500">{!! str_repeat('★', $stars) . str_repeat('☆', 5 - $stars) !!}</div>
                    <figcaption class="text-left">
                        <span class="type-tagline block text-slate-900">{{ $review->author }}</span>
                        @if (filled($review->neighborhood))
                            <span class="type-body-sm block text-emerald-800">{{ $review->neighborhood }}</span>
                        @endif
                    </figcaption>
                </div>
                <p class="type-tagline mt-[var(--space-md)] inline-flex items-center gap-1 text-emerald-800">
                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Verified Local Review
                </p>
            </figure>
        @else
            <p class="type-body-md stack-after-header text-center text-emerald-100/80">A featured review will appear here soon.</p>
        @endif
    </div>
</section>
