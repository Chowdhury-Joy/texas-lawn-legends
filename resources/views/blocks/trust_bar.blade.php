@php $trustBadges = (array) ($data['badges'] ?? []); @endphp
@if (! empty($trustBadges))
    <section data-reveal class="border-y-4 border-slate-950 bg-slate-950 text-slate-100">
        <div class="relative overflow-hidden py-[var(--space-md)]">
            <div class="marquee-track flex w-max items-center gap-container-xl px-[var(--space-lg)] will-change-transform">
                @foreach ($trustBadges as $badge)
                    <div class="type-tagline flex shrink-0 items-center gap-2 text-slate-100">
                        <svg class="h-5 w-5 shrink-0 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        <span>{{ $badge }}</span>
                    </div>
                @endforeach
                @foreach ($trustBadges as $badge)
                    <div aria-hidden="true" class="type-tagline flex shrink-0 items-center gap-2 text-slate-100">
                        <svg class="h-5 w-5 shrink-0 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        <span>{{ $badge }}</span>
                    </div>
                @endforeach
            </div>
            <div class="pointer-events-none absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-slate-950 to-transparent"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-slate-950 to-transparent"></div>
        </div>
    </section>
@endif
