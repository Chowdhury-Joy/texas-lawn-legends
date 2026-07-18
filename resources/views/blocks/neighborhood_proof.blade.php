<section id="portfolio" class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-7xl px-6 py-20"
         x-data="{ active: 'All Neighborhoods' }">
        <h2 data-field="heading" class="text-center text-4xl font-medium tracking-tighter text-slate-900 sm:text-5xl">{{ $data['heading'] ?? 'Verified Local Proof' }}</h2>
        @if ($proofSub = $data['subheading'] ?? null)
            <p data-field="subheading" class="mx-auto mt-3 max-w-2xl text-center text-slate-600">{{ $proofSub }}</p>
        @endif
 
        <div class="mt-10 flex flex-wrap justify-center gap-2">
            @foreach (collect(['All Neighborhoods'])->merge($neighborhoods) as $tab)
                <button type="button"
                        @click="active = @js($tab)"
                        :class="active === @js($tab) ? 'bg-slate-950 text-yellow-400' : 'bg-white text-slate-900 hover:bg-yellow-400'"
                        class="border-2 border-slate-950 px-4 py-2 text-xs font-medium uppercase tracking-wide transition-colors">
                    {{ $tab }}
                </button>
            @endforeach
        </div>
 
        <div x-cloak class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-2 tab:grid-cols-2 lg:grid-cols-3">
            @forelse ($testimonials as $review)
                <div class="box-brutal flex flex-col mr-[8px] md:mr-0"
                     x-show="active === 'All Neighborhoods' || active === @js($review->neighborhood)"
                     x-transition>
                    <div class="grid grid-cols-2 border-b-2 border-slate-950">
                        <div class="flex aspect-square items-center justify-center border-r-2 border-slate-950 bg-slate-200">
                            <span class="text-xs font-black uppercase tracking-widest text-slate-500">Before</span>
                        </div>
                        <div class="flex aspect-square items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-900">
                            <span class="text-xs font-black uppercase tracking-widest text-yellow-400">After</span>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
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
