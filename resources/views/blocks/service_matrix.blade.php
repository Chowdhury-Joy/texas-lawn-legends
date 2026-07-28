@php
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
{{-- CREATE SUITE --}}
<section id="create-suite" class="border-t-4 border-slate-950 bg-emerald-900 px-6 py-section text-on-primary">
    <div class="mx-auto max-w-7xl">
        <span data-reveal class="chip-accent bg-yellow-400 inline-block px-3 py-1 text-xs font-black uppercase tracking-widest">{{ niche_label('suite_create') }}</span>
        <h2 data-field="create_suite_heading" data-reveal class="mt-5 text-3xl font-medium leading-tight tracking-tighter sm:text-4xl lg:text-5xl max-w-3xl text-on-primary">{{ $data['create_suite_heading'] ?? 'Premium Landscape Design & Structural Hardscaping' }}</h2>

        <div class="mt-10 {{ $layout ?: "grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" }}">
            @foreach ($createServices as $service)
                <div data-stagger style="--stagger-i: {{ $loop->index }}" class="border-2 border-emerald-950 bg-emerald-800/40 p-4 sm:p-6 shadow-brutal-forest mr-[6px] sm:mr-0 flex flex-col justify-between text-on-primary-light">
                    <div>
                        @if (filled($service->image))
                            <img src="{{ public_url($service->image) }}" alt="{{ $service->title }}" class="h-10 w-10 object-cover border-2 border-slate-950 bg-slate-100 shadow-brutal-sm mb-3">
                        @elseif (filled($service->icon))
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-accent-ink" />
                        @endif
                        <h3 class="mt-4 text-xl font-medium tracking-tighter text-on-primary-light">{{ $service->title }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-on-primary-light/80">{{ $service->short_description }}</p>
                    </div>
                    <p class="mt-6 text-[11px] font-bold uppercase tracking-widest text-on-primary-light">Custom pricing · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CARE SUITE --}}
<section id="care-suite" class="border-t-4 border-slate-950 bg-white px-6 py-section text-slate-900">
    <div class="mx-auto max-w-7xl">
        <span data-reveal class="chip-accent bg-yellow-400 inline-block px-3 py-1 text-xs font-black uppercase tracking-widest">{{ niche_label('suite_care') }}</span>
        <h2 data-field="care_suite_heading" data-reveal class="mt-5 text-3xl font-medium leading-tight tracking-tighter sm:text-4xl lg:text-5xl max-w-3xl text-slate-950">{{ $data['care_suite_heading'] ?? 'Comprehensive Property Preservation & Lawn Maintenance' }}</h2>
        
        <div class="mt-10 {{ $layout ?: "grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" }}">
            @foreach ($careServices as $service)
                <div data-stagger style="--stagger-i: {{ $loop->index }}" class="border-2 border-slate-950 bg-white p-4 sm:p-6 shadow-brutal-forest mr-[6px] sm:mr-0 flex flex-col justify-between">
                    <div>
                        @if (filled($service->image))
                            <img src="{{ public_url($service->image) }}" alt="{{ $service->title }}" class="h-10 w-10 object-cover border-2 border-slate-950 bg-slate-100 shadow-brutal-sm mb-3">
                        @elseif (filled($service->icon))
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-accent-ink" />
                        @endif
                        <h3 class="mt-4 text-xl font-medium tracking-tighter text-slate-950">{{ $service->title }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-700">{{ $service->short_description }}</p>
                    </div>
                    <div>
                        <p class="mt-6 text-[11px] font-bold uppercase tracking-widest text-on-accent bg-yellow-400/20 px-2.5 py-1 inline-block">Program rate · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if (product_part_at_least(2))
            {{-- Interactive Quick Price Preview Mini-Widget --}}
            <div class="mt-12 box-brutal bg-slate-950 p-6 text-white"
                 x-data="{
                     sqft: 1200,
                     low() { return Math.round(this.sqft * 0.85); },
                     high() { return Math.round(this.sqft * 1.45); }
                 }">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <span class="chip-accent bg-yellow-400 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest">Quick Calculator</span>
                        <h3 class="mt-2 text-xl font-black uppercase tracking-tight text-white sm:text-2xl">Instant Price Range Preview</h3>
                        <p class="mt-1 text-xs text-slate-400">Slide to estimate range before launching full booking wizard.</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Estimated Range</span>
                        <p class="text-3xl font-black text-[#f2f2f2]">$<span x-text="low().toLocaleString()"></span> <span class="text-white/50">–</span> $<span x-text="high().toLocaleString()"></span></p>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="mb-1.5 flex justify-between text-xs font-bold text-slate-400">
                        <span>{{ niche_label('size_field') }}</span>
                        <span class="font-mono text-[#f2f2f2]"><span x-text="Number(sqft).toLocaleString()"></span> {{ niche_label('size_unit') }}</span>
                    </div>
                    <input type="range" min="300" max="5000" step="100" x-model.number="sqft" class="h-3 w-full cursor-pointer appearance-none border-2 border-slate-950 bg-slate-800 accent-yellow-400">
                </div>
                <div class="mt-6 flex justify-end">
                    <a :href="'{{ url('/estimate') }}?sqft=' + sqft" class="btn-brutal btn-primary bg-yellow-400 px-6 py-3 text-xs">
                        Continue With <span x-text="Number(sqft).toLocaleString()"></span> {{ niche_label('size_unit') }} Estimate →
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
