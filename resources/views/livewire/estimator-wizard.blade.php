<div class="mx-auto max-w-4xl">

    {{-- Progress rail --}}
    @php
        $labels = ['Details', 'Scope', 'Dimensions', 'Your Estimate'];
        $progressPct = match ($step) {
            1 => '0%',
            2 => '33.33%',
            3 => '66.66%',
            4 => '100%',
            default => '0%',
        };
    @endphp
    <div class="relative mb-10">
        <div class="absolute left-0 top-5 -z-0 h-1.5 w-full bg-slate-200">
            <div class="h-full bg-yellow-400 transition-all duration-500 ease-in-out" style="width: {{ $progressPct }}"></div>
        </div>
        <div class="relative z-10 grid grid-cols-4 gap-2">
            @foreach ($labels as $i => $label)
                @php $n = $i + 1; @endphp
                <div class="flex flex-col items-center text-center">
                    <div @class([
                        'flex h-10 w-10 items-center justify-center border-2 border-slate-950 text-sm font-black transition-colors duration-300',
                        'bg-yellow-400 text-slate-950' => $step >= $n,
                        'bg-white text-slate-400' => $step < $n,
                    ])>{{ $n }}</div>
                    <span @class([
                        'mt-2 text-[11px] font-black uppercase tracking-widest transition-colors duration-300',
                        'text-slate-900' => $step >= $n,
                        'text-slate-400' => $step < $n,
                    ])>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="box-brutal p-6 sm:p-10">

        {{-- ================= STEP 1: Contact + Neighborhood ================= --}}
        @if ($step === 1)
            <h2 class="text-2xl font-black uppercase tracking-tight text-slate-900 sm:text-3xl">Let's verify your service zone</h2>
            <p class="mt-2 text-slate-600">Tell us where the project is and how to reach you.</p>

            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-700">Full name</label>
                    <input type="text" wire:model="name" class="mt-2 w-full border-2 border-slate-950 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Jordan Rivera">
                    @error('name') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-700">Email</label>
                    <input type="email" wire:model="email" class="mt-2 w-full border-2 border-slate-950 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="you@example.com">
                    @error('email') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-700">Phone</label>
                    <input type="tel" wire:model="phone" class="mt-2 w-full border-2 border-slate-950 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="(214) 555-0100">
                    @error('phone') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-700">Neighborhood</label>
                    <select wire:model="neighborhood" class="mt-2 w-full border-2 border-slate-950 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select your neighborhood…</option>
                        @foreach ($this->neighborhoods as $area)
                            <option value="{{ $area }}">{{ $area }}</option>
                        @endforeach
                    </select>
                    @error('neighborhood') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-700">Property address <span class="text-slate-400">(optional)</span></label>
                    <input type="text" wire:model="address" class="mt-2 w-full border-2 border-slate-950 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="1420 Kessler Pkwy, Dallas, TX">
                    @error('address') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        {{-- ================= STEP 2: Service Scope ================= --}}
        @if ($step === 2)
            <h2 class="text-2xl font-black uppercase tracking-tight text-slate-900 sm:text-3xl">What are we building?</h2>
            <p class="mt-2 text-slate-600">Pick the primary scope for your project.</p>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($this->services as $service)
                    <button type="button" wire:click="selectService({{ $service->id }})"
                            @class([
                                'flex items-start gap-3 border-2 border-slate-950 p-4 text-left transition-all',
                                'bg-yellow-400' => $service_id === $service->id,
                                'bg-white hover:bg-slate-50' => $service_id !== $service->id,
                            ])>
                        <x-svg-icon :name="$service->icon" class="h-7 w-7 shrink-0 text-slate-900" />
                        <span>
                            <span class="block text-sm font-black uppercase tracking-tight text-slate-900">{{ $service->title }}</span>
                            <span class="mt-1 block text-xs leading-snug text-slate-600">{{ $service->short_description }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
            @error('service_id') <p class="mt-3 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
        @endif

        {{-- ================= STEP 3: Dimensions + Complexity ================= --}}
        @if ($step === 3)
            <h2 class="text-2xl font-black uppercase tracking-tight text-slate-900 sm:text-3xl">Project dimensions</h2>
            <p class="mt-2 text-slate-600">Drag to estimate the area, then tell us how involved it is.</p>

            <div class="mt-8" x-data="{ v: @entangle('sqft').live }">
                <div class="flex items-baseline justify-between">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-700">Approx. area</label>
                    <span class="text-3xl font-black text-slate-900"><span x-text="Number(v).toLocaleString()"></span> <span class="text-base text-slate-500">sq ft</span></span>
                </div>
                <input type="range" min="{{ $this->sqftBounds['min'] }}" max="{{ $this->sqftBounds['max'] }}" step="50"
                       x-model.number="v"
                       class="mt-4 h-3 w-full cursor-pointer appearance-none border-2 border-slate-950 bg-slate-100 accent-yellow-400">
                <div class="mt-1 flex justify-between text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    <span>{{ number_format($this->sqftBounds['min']) }}</span>
                    <span>{{ number_format($this->sqftBounds['max']) }}+</span>
                </div>
                @error('sqft') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-700">Complexity</label>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach (['simple' => 'Simple', 'standard' => 'Standard', 'complex' => 'Complex'] as $value => $label)
                        <button type="button" wire:click="$set('complexity', '{{ $value }}')"
                                @class([
                                    'border-2 border-slate-950 px-4 py-3 text-sm font-black uppercase tracking-wide transition-all',
                                    'bg-slate-950 text-yellow-400' => $complexity === $value,
                                    'bg-white text-slate-900 hover:bg-slate-50' => $complexity !== $value,
                                ])>{{ $label }}</button>
                    @endforeach
                </div>
                @error('complexity') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
            </div>

            @if ($preview = $this->liveEstimatePreview)
                <div class="mt-8 border-2 border-slate-950 bg-slate-950 p-4 text-center text-white" style="box-shadow: 4px 4px 0px 0px rgba(250,204,21,1);">
                    <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-widest text-slate-400">
                        <span>Live Ticker</span>
                        <span class="inline-flex items-center gap-1.5 text-yellow-400">
                            <span class="h-2 w-2 rounded-full bg-yellow-400 animate-pulse"></span>
                            Real-time valuation preview
                        </span>
                    </div>
                    <p class="mt-2 text-2xl font-black text-yellow-400">
                        @if ($preview['is_custom'])
                            Custom Quote Required
                        @else
                            ${{ number_format($preview['low']) }} <span class="text-slate-400">–</span> ${{ number_format($preview['high']) }}
                        @endif
                    </p>
                </div>
            @endif
        @endif

        {{-- ================= STEP 4: Value Gate / Booking ================= --}}
        @if ($step === 4)
            @if ($booked)
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center border-4 border-slate-950 bg-yellow-400">
                        <svg class="h-8 w-8 text-slate-950" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    </div>
                    <h2 class="mt-6 text-2xl font-black uppercase tracking-tight text-slate-900 sm:text-3xl">You're on the schedule!</h2>
                    <p class="mt-3 text-slate-600">Your site visit is locked in for
                        <span class="font-black text-slate-900">{{ \Illuminate\Support\Carbon::parse($selectedDate)->format('l, F j') }} at {{ $selectedTime }}</span>.
                        We've sent the details to {{ $email }}.
                    </p>
                    <a href="{{ url('/') }}" class="btn-brutal mt-8 inline-block bg-yellow-400 px-6 py-3 text-sm text-slate-950">Back to Home</a>
                </div>
            @elseif ($isCustom)
                <div class="border-4 border-slate-950 bg-emerald-900 p-8 text-center text-white" style="box-shadow: 6px 6px 0px 0px rgba(250,204,21,1);">
                    <span class="inline-block bg-yellow-400 px-3 py-1 text-xs font-black uppercase tracking-widest text-slate-950">Unique Project</span>
                    <h2 class="mt-4 text-2xl font-black uppercase tracking-tight sm:text-3xl">This project is unique!</h2>
                    <p class="mx-auto mt-3 max-w-lg text-emerald-100">Let's get an expert to look at it personally. Book a custom consultation and we'll build a precise scope with you.</p>
                </div>
                <div class="mt-8">
                    @include('livewire.partials.booking-grid')
                </div>
            @else
                <div class="text-center" x-data="{
                    targetLow: {{ (int) ($estimateLow ?? 0) }},
                    targetHigh: {{ (int) ($estimateHigh ?? 0) }},
                    currentLow: 0,
                    currentHigh: 0,
                    init() {
                        const duration = 800;
                        const start = performance.now();
                        const step = (now) => {
                            const progress = Math.min((now - start) / duration, 1);
                            const ease = 1 - Math.pow(1 - progress, 3);
                            this.currentLow = Math.floor(ease * this.targetLow);
                            this.currentHigh = Math.floor(ease * this.targetHigh);
                            if (progress < 1) {
                                requestAnimationFrame(step);
                            }
                        };
                        requestAnimationFrame(step);
                    }
                }">
                    <span class="inline-block bg-emerald-900 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400">Your Instant Estimate</span>
                    <h2 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                        $<span x-text="currentLow.toLocaleString()"></span> <span class="text-slate-400">–</span> $<span x-text="currentHigh.toLocaleString()"></span>
                    </h2>
                    <p class="mx-auto mt-3 max-w-lg text-slate-600">
                        Based on {{ number_format($sqft) }} sq ft of {{ optional($this->services->firstWhere('id', $service_id))->title }} in {{ $neighborhood }}.
                        Lock in a free 30-minute site visit below to confirm your exact scope — your details are already saved.
                    </p>
                </div>
                <div class="mt-8">
                    @include('livewire.partials.booking-grid')
                </div>
            @endif
        @endif

        {{-- ================= NAV ================= --}}
        @if ($step < 4)
            <div class="mt-10 flex items-center justify-between">
                @if ($step > 1)
                    <button type="button" wire:click="previousStep" class="border-2 border-slate-950 bg-white px-5 py-3 text-sm font-black uppercase tracking-wide text-slate-900 transition-colors hover:bg-slate-100">← Back</button>
                @else
                    <span></span>
                @endif
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                        class="btn-brutal bg-yellow-400 px-7 py-3 text-sm text-slate-950">
                    <span wire:loading.remove wire:target="nextStep">{{ $step === 3 ? 'Calculate My Estimate' : 'Continue' }} →</span>
                    <span wire:loading wire:target="nextStep">Working…</span>
                </button>
            </div>
        @endif

    </div>
</div>
