@php
    $reviews = $testimonials ?? collect();
    if (! empty($data['featured_only'])) {
        $reviews = $reviews->where('is_featured', true);
    }
    $reviews = $reviews->values();
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-7xl space-section">
        <div class="stack-header">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-emerald-900 px-2 py-0.5 type-tagline text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif
            @if (filled($data['heading'] ?? null))
                <h2 data-field="heading" data-reveal class="type-h2 text-slate-900">
                    {{ $data['heading'] }}
                </h2>
            @endif
            @if (filled($data['subheading'] ?? null))
                <p data-field="subheading" data-reveal class="type-body-lg max-w-2xl text-slate-600">{{ $data['subheading'] }}</p>
            @endif
        </div>

        @if ($reviews->isNotEmpty())
            <div class="stack-after-header {{ $layout ?: 'grid-services' }}">
                @foreach ($reviews as $review)
                    <figure data-stagger style="--stagger-i: {{ $loop->index }}" class="box-brutal stack-card space-card">
                        @php $stars = max(0, min(5, (int) $review->rating)); @endphp
                        <div class="text-yellow-500">{!! str_repeat('★', $stars) . str_repeat('☆', 5 - $stars) !!}</div>
                        <blockquote class="type-body-md flex-1 text-slate-700">“{{ $review->review_text }}”</blockquote>
                        <figcaption class="border-t-2 border-slate-200 pt-[var(--space-md)]">
                            <p class="type-tagline text-slate-900">{{ $review->author }}</p>
                            @if (filled($review->neighborhood))
                                <p class="type-tagline mt-1 inline-flex items-center gap-1 text-emerald-800">
                                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    Verified · {{ $review->neighborhood }}
                                </p>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @else
            <p class="type-body-md stack-after-header text-center text-slate-500">Featured reviews will appear here soon.</p>
        @endif
    </div>
</section>
