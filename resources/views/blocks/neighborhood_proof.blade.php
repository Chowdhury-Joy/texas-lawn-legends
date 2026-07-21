@php
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section id="portfolio" class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-7xl px-6 py-section">
        <h2 data-field="heading" data-reveal class="text-center text-4xl font-medium tracking-tighter text-slate-900 sm:text-5xl">{{ $data['heading'] ?? 'Verified Local Proof' }}</h2>
        @if ($proofSub = $data['subheading'] ?? null)
            <p data-field="subheading" data-reveal class="mx-auto mt-3 max-w-2xl text-center text-slate-600">{{ $proofSub }}</p>
        @endif

        <div class="mt-10 {{ $layout ?: 'grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3' }}">
            @forelse ($testimonials as $review)
                <div data-stagger style="--stagger-i: {{ $loop->index }}" class="box-brutal flex flex-col p-4 sm:p-6">
                    <div class="relative aspect-square overflow-hidden border-b-2 border-slate-950 select-none"
                         x-data="{ pos: 50, dragging: false }"
                         @mouseleave="dragging = false"
                         @mouseup="dragging = false"
                         @mousemove="if (dragging) {
                             let rect = $el.getBoundingClientRect();
                             let x = event.clientX - rect.left;
                             pos = Math.max(0, Math.min(100, (x / rect.width) * 100));
                         }"
                         @touchmove="if (dragging) {
                             let rect = $el.getBoundingClientRect();
                             let x = event.touches[0].clientX - rect.left;
                             pos = Math.max(0, Math.min(100, (x / rect.width) * 100));
                         }">

                        {{-- After Layer (Full Width Background) --}}
                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-900">
                            <span class="bg-slate-950/80 px-2.5 py-1 text-xs font-black uppercase tracking-widest text-yellow-400">After Build</span>
                        </div>

                        {{-- Before Layer (Clipped Width) --}}
                        <div class="absolute inset-y-0 left-0 overflow-hidden border-r-2 border-slate-950 bg-slate-300" :style="'width: ' + pos + '%'">
                            <div class="absolute inset-0 flex h-full w-full items-center justify-center bg-slate-200">
                                <span class="bg-white/80 px-2.5 py-1 text-xs font-black uppercase tracking-widest text-slate-800">Before Build</span>
                            </div>
                        </div>

                        {{-- Slider Handle Divider --}}
                        <div class="absolute inset-y-0 flex cursor-ew-resize items-center justify-center bg-yellow-400"
                             style="width: 4px;"
                             :style="'left: calc(' + pos + '% - 2px)'"
                             @mousedown="dragging = true"
                             @touchstart="dragging = true">
                            <div class="flex h-7 w-7 items-center justify-center border-2 border-slate-950 bg-yellow-400 text-xs font-black text-slate-950 shadow-md">
                                ↔
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col pt-6">
                        <div class="text-yellow-500">{!! str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating) !!}</div>
                        <blockquote class="mt-3 flex-1 text-sm leading-relaxed text-slate-700">“{{ $review->review_text }}”</blockquote>
                        <div class="mt-4 border-t-2 border-slate-200 pt-4">
                            <p class="text-sm font-medium tracking-tighter text-slate-900">{{ $review->author }}</p>
                            <p class="mt-1 inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-widest text-emerald-800">
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                Verified Review
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-slate-500">Featured reviews will appear here soon.</p>
            @endforelse
        </div>
    </div>
</section>
