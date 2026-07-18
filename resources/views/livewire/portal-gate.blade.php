<div class="mx-auto max-w-5xl">

    @if (! $unlocked)
        {{-- ================= STATE A: LOCKED ================= --}}
        <div class="mx-auto max-w-xl">
            <div class="box-brutal p-8 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center border-2 border-slate-950 bg-slate-950">
                    <svg class="h-7 w-7 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                </div>
                <h2 class="mt-6 text-xl font-black uppercase tracking-tight text-slate-900 sm:text-2xl">Members Only</h2>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-slate-600">
                    Enter your monthly access token to unlock the member portal and seasonal pricing tiers.
                </p>

                <form wire:submit="unlock" class="mt-8">
                    <input type="text" wire:model="code"
                           class="w-full border-2 border-slate-950 px-4 py-3 text-center font-black uppercase tracking-widest text-slate-900 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                           placeholder="ENTER TOKEN">
                    @error('code') <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p> @enderror

                    <button type="submit" wire:loading.attr="disabled"
                            class="btn-brutal mt-5 w-full bg-yellow-400 px-6 py-3 text-sm text-slate-950">
                        <span wire:loading.remove wire:target="unlock">Unlock Portal →</span>
                        <span wire:loading wire:target="unlock">Verifying…</span>
                    </button>
                </form>

                <p class="mt-5 text-xs text-slate-400">Tokens are issued monthly. Need one? Call {{ setting('primary_phone') }}.</p>
            </div>
        </div>
    @else
        {{-- ================= STATE B: UNLOCKED ================= --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <span class="inline-block bg-emerald-900 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400">Portal Unlocked</span>
                <h2 class="mt-4 text-2xl font-black uppercase tracking-tight text-slate-900 sm:text-3xl">Seasonal Add-On Menu</h2>
                <p class="mt-2 text-slate-600">One click orders an add-on — we handle the rest and confirm by phone.</p>
            </div>
            <button type="button" wire:click="lock" class="border-2 border-slate-950 bg-white px-4 py-2 text-xs font-black uppercase tracking-wide text-slate-900 transition-colors hover:bg-slate-100">Lock Portal</button>
        </div>

        @if ($lastOrdered)
            <div class="mt-6 border-4 border-slate-950 bg-yellow-400 px-5 py-4" wire:key="flash-{{ count($ordered) }}">
                <p class="text-sm font-black uppercase tracking-wide text-slate-950">
                    ✓ Order placed for “{{ $lastOrdered }}” — operations has been alerted and will confirm shortly.
                </p>
            </div>
        @endif

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($this->addons as $addon)
                @php $alreadyOrdered = in_array($addon->title, $ordered, true); @endphp
                <div class="box-brutal flex flex-col p-6">
                    <h3 class="text-base font-black uppercase tracking-tight text-slate-900">{{ $addon->title }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ $addon->description }}</p>
                    <div class="mt-4 flex items-baseline gap-1">
                        <span class="text-2xl font-black text-slate-900">${{ number_format($addon->base_price, 2) }}</span>
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">/ {{ $addon->price_unit }}</span>
                    </div>
                    <button type="button"
                            wire:click="order({{ $addon->id }})"
                            wire:loading.attr="disabled"
                            @disabled($alreadyOrdered)
                            @class([
                                'mt-5 border-2 border-slate-950 px-4 py-2.5 text-sm font-black uppercase tracking-wide transition-all',
                                'bg-slate-950 text-yellow-400 cursor-default' => $alreadyOrdered,
                                'bg-yellow-400 text-slate-950 hover:translate-x-[1px] hover:translate-y-[1px]' => ! $alreadyOrdered,
                            ])>
                        {{ $alreadyOrdered ? '✓ Ordered' : 'Order Now' }}
                    </button>
                </div>
            @empty
                <p class="col-span-full text-center text-slate-500">No add-ons are available right now — check back soon.</p>
            @endforelse
        </div>
    @endif

</div>
