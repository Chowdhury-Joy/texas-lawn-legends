{{-- Localized site-visit booking matrix --}}
<h3 class="text-center text-xl font-black uppercase tracking-tight text-slate-900">Pick your site-visit time</h3>
<p class="mx-auto mt-1 max-w-md text-center text-sm text-slate-500">Free 30-minute walkthrough. No obligation, no friction.</p>

<div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
    @foreach ($this->bookingSlots as $day)
        <div class="border-2 border-slate-950">
            <div class="border-b-2 border-slate-950 bg-slate-950 px-2 py-2 text-center text-[11px] font-black uppercase tracking-widest text-yellow-400">
                {{ $day['label'] }}
            </div>
            <div class="space-y-2 p-2">
                @foreach ($day['times'] as $time)
                    <button type="button"
                            wire:click="book('{{ $day['date'] }}', '{{ $time }}')"
                            wire:loading.attr="disabled"
                            class="block w-full border-2 border-slate-950 bg-white px-2 py-2 text-xs font-black uppercase tracking-wide text-slate-900 transition-all hover:bg-yellow-400 hover:translate-x-[1px] hover:translate-y-[1px]">
                        {{ $time }}
                    </button>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
