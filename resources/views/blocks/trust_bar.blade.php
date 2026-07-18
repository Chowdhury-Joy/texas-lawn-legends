@php $trustBadges = (array) ($data['badges'] ?? []); @endphp
@if (! empty($trustBadges))
    <section class="border-y-4 border-slate-950 bg-slate-100">
        <div class="mx-auto grid max-w-7xl grid-cols-2 items-center justify-items-center gap-6 px-6 py-6 opacity-80 md:grid-cols-4">
            @foreach ($trustBadges as $badge)
                <div class="flex items-center gap-2 text-center text-xs font-black uppercase tracking-wide text-slate-700">
                    <svg class="h-5 w-5 shrink-0 text-emerald-800" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    <span>{{ $badge }}</span>
                </div>
            @endforeach
        </div>
    </section>
@endif
