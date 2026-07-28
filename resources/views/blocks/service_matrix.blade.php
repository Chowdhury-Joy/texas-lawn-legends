@php
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
{{-- CREATE SUITE --}}
<section id="create-suite" class="border-t-4 border-slate-950 bg-emerald-900 space-section text-on-primary">
    <div class="mx-auto max-w-7xl">
        <div class="stack-header">
            <span data-reveal class="chip-accent type-tagline bg-yellow-400 inline-block px-3 py-1">{{ niche_label('suite_create') }}</span>
            <h2 data-field="create_suite_heading" data-reveal class="type-h2 max-w-3xl text-on-primary">{{ $data['create_suite_heading'] ?? 'Premium Landscape Design & Structural Hardscaping' }}</h2>
        </div>

        <div class="stack-after-header {{ $layout ?: 'grid-services' }}">
            @foreach ($createServices as $service)
                <div data-stagger style="--stagger-i: {{ $loop->index }}" class="stack-card space-card border-2 border-emerald-950 bg-emerald-800/40 shadow-brutal-forest mr-[6px] sm:mr-0 text-on-primary-light">
                    <div class="stack-card-body">
                        @if (filled($service->image))
                            <img src="{{ public_url($service->image) }}" alt="{{ $service->title }}" class="h-10 w-10 object-cover border-2 border-slate-950 bg-slate-100 shadow-brutal-sm">
                        @elseif (filled($service->icon))
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-accent-ink" />
                        @endif
                        <h3 class="type-h4 text-on-primary-light">{{ $service->title }}</h3>
                        <p class="type-body-lg text-on-primary-light/80">{{ $service->short_description }}</p>
                    </div>
                    <p class="type-tagline text-on-primary-light">Custom pricing · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CARE SUITE --}}
<section id="care-suite" class="border-t-4 border-slate-950 bg-white space-section text-slate-900">
    <div class="mx-auto max-w-7xl">
        <div class="stack-header">
            <span data-reveal class="chip-accent type-tagline bg-yellow-400 inline-block px-3 py-1">{{ niche_label('suite_care') }}</span>
            <h2 data-field="care_suite_heading" data-reveal class="type-h2 max-w-3xl text-slate-950">{{ $data['care_suite_heading'] ?? 'Comprehensive Property Preservation & Lawn Maintenance' }}</h2>
        </div>

        <div class="stack-after-header {{ $layout ?: 'grid-services' }}">
            @foreach ($careServices as $service)
                <div data-stagger style="--stagger-i: {{ $loop->index }}" class="stack-card space-card border-2 border-slate-950 bg-white shadow-brutal-forest mr-[6px] sm:mr-0">
                    <div class="stack-card-body">
                        @if (filled($service->image))
                            <img src="{{ public_url($service->image) }}" alt="{{ $service->title }}" class="h-10 w-10 object-cover border-2 border-slate-950 bg-slate-100 shadow-brutal-sm">
                        @elseif (filled($service->icon))
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-accent-ink" />
                        @endif
                        <h3 class="type-h4 text-slate-950">{{ $service->title }}</h3>
                        <p class="type-body-lg text-slate-700">{{ $service->short_description }}</p>
                    </div>
                    <div>
                        <p class="type-tagline text-on-accent bg-yellow-400/20 px-2.5 py-1 inline-block">Program rate · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if (product_part_at_least(2))
            {{-- Interactive Quick Price Preview Mini-Widget --}}
            <div class="stack-after-header box-brutal space-card bg-slate-950 text-white"
                 x-data="{
                     sqft: 1200,
                     low() { return Math.round(this.sqft * 0.85); },
                     high() { return Math.round(this.sqft * 1.45); }
                 }">
                <div class="flex flex-col justify-between gap-container-lg sm:flex-row sm:items-center">
                    <div class="stack-header">
                        <span class="chip-accent type-tagline bg-yellow-400 px-2.5 py-1">Quick Calculator</span>
                        <h3 class="type-h4 text-white">Instant Price Range Preview</h3>
                        <p class="type-body-sm text-slate-400">Slide to estimate range before launching full booking wizard.</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="type-tagline text-slate-400">Estimated Range</span>
                        <p class="type-h3 text-[#f2f2f2]">$<span x-text="low().toLocaleString()"></span> <span class="text-white/50">–</span> $<span x-text="high().toLocaleString()"></span></p>
                    </div>
                </div>
                <div class="mt-[var(--space-xl)]">
                    <div class="mb-[var(--space-xs)] flex justify-between type-body-sm text-slate-400">
                        <span>{{ niche_label('size_field') }}</span>
                        <span class="type-tagline text-[#f2f2f2]"><span x-text="Number(sqft).toLocaleString()"></span> {{ niche_label('size_unit') }}</span>
                    </div>
                    <input type="range" min="300" max="5000" step="100" x-model.number="sqft" class="h-3 w-full cursor-pointer appearance-none border-2 border-slate-950 bg-slate-800 accent-yellow-400">
                </div>
                <div class="mt-[var(--space-xl)] flex justify-end">
                    <a :href="'{{ url('/estimate') }}?sqft=' + sqft" class="btn-brutal btn-primary type-btn bg-yellow-400 px-6 py-3">
                        Continue With <span x-text="Number(sqft).toLocaleString()"></span> {{ niche_label('size_unit') }} Estimate →
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
