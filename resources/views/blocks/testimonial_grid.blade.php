@php
    $reviews = $testimonials ?? collect();
    if (! empty($data['featured_only'])) {
        $reviews = $reviews->where('is_featured', true);
    }
    $reviews = $reviews->values();
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-7xl px-6 py-section">
        @if (filled($data['eyebrow'] ?? null))
            <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                {{ $data['eyebrow'] }}
            </span>
        @endif
        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" data-reveal class="mt-4 text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                {{ $data['heading'] }}
            </h2>
        @endif
        @if (filled($data['subheading'] ?? null))
            <p data-field="subheading" data-reveal class="mt-3 max-w-2xl text-slate-600">{{ $data['subheading'] }}</p>
        @endif

        @if ($reviews->isNotEmpty())
            <div class="mt-10 {{ $layout ?: 'grid grid-cols-1 gap-6 lg:grid-cols-3' }}">
                @foreach ($reviews as $review)
                    <figure data-stagger style="--stagger-i: {{ $loop->index }}" class="box-brutal flex flex-col p-4 sm:p-6">
                        @php $stars = max(0, min(5, (int) $review->rating)); @endphp
                        <div class="text-yellow-500">{!! str_repeat('★', $stars) . str_repeat('☆', 5 - $stars) !!}</div>
                        <blockquote class="mt-3 flex-1 text-sm leading-relaxed text-slate-700">“{{ $review->review_text }}”</blockquote>
                        <figcaption class="mt-4 border-t-2 border-slate-200 pt-4">
                            <p class="text-sm font-medium tracking-tighter text-slate-900">{{ $review->author }}</p>
                            @if (filled($review->neighborhood))
                                <p class="mt-1 inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-widest text-emerald-800">
                                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    Verified · {{ $review->neighborhood }}
                                </p>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @else
            <p class="mt-10 text-center text-slate-500">Featured reviews will appear here soon.</p>
        @endif
    </div>
</section>
