@php
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
{{-- CREATE SUITE --}}
<section id="create-suite" class="border-t-4 border-slate-950 bg-emerald-900 px-6 py-section text-white">
    <div class="mx-auto max-w-7xl">
        <span class="inline-block bg-yellow-400 px-3 py-1 text-xs font-black uppercase tracking-widest text-slate-950">The Create Suite</span>
        <h2 data-field="create_suite_heading" class="mt-5 text-3xl font-medium leading-tight tracking-tighter sm:text-4xl lg:text-5xl max-w-3xl">{{ $data['create_suite_heading'] ?? 'Premium Landscape Design & Structural Hardscaping' }}</h2>
        
        <div class="mt-10 {{ $layout ?: "grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" }}">
            @foreach ($createServices as $service)
                <div data-stagger style="--stagger-i: {{ $loop->index }}" class="border-2 border-emerald-950 bg-emerald-800/40 p-4 sm:p-6 shadow-brutal-forest mr-[6px] sm:mr-0 flex flex-col justify-between">
                    <div>
                        @if (filled($service->image))
                            <img src="{{ public_url($service->image) }}" alt="{{ $service->title }}" class="h-10 w-10 object-cover border-2 border-slate-950 bg-slate-100 shadow-brutal-sm mb-3">
                        @elseif (filled($service->icon))
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-yellow-400" />
                        @endif
                        <h3 class="mt-4 text-xl font-medium tracking-tighter text-white">{{ $service->title }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-emerald-100/90">{{ $service->short_description }}</p>
                    </div>
                    <p class="mt-6 text-[11px] font-bold uppercase tracking-widest text-yellow-400">Custom pricing · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CARE SUITE --}}
<section id="care-suite" class="border-t-4 border-slate-950 bg-white px-6 py-section text-slate-900">
    <div class="mx-auto max-w-7xl">
        <span class="inline-block bg-yellow-400 px-3 py-1 text-xs font-black uppercase tracking-widest text-slate-950">The Care Suite</span>
        <h2 data-field="care_suite_heading" class="mt-5 text-3xl font-medium leading-tight tracking-tighter sm:text-4xl lg:text-5xl max-w-3xl text-slate-950">{{ $data['care_suite_heading'] ?? 'Comprehensive Property Preservation & Lawn Maintenance' }}</h2>
        
        <div class="mt-10 {{ $layout ?: "grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" }}">
            @foreach ($careServices as $service)
                <div data-stagger style="--stagger-i: {{ $loop->index }}" class="border-2 border-slate-950 bg-white p-4 sm:p-6 shadow-brutal-forest mr-[6px] sm:mr-0 flex flex-col justify-between">
                    <div>
                        @if (filled($service->image))
                            <img src="{{ public_url($service->image) }}" alt="{{ $service->title }}" class="h-10 w-10 object-cover border-2 border-slate-950 bg-slate-100 shadow-brutal-sm mb-3">
                        @elseif (filled($service->icon))
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-yellow-500" />
                        @endif
                        <h3 class="mt-4 text-xl font-medium tracking-tighter text-slate-950">{{ $service->title }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-700">{{ $service->short_description }}</p>
                    </div>
                    <div>
                        <p class="mt-6 text-[11px] font-bold uppercase tracking-widest text-slate-950 bg-yellow-400/20 px-2.5 py-1 inline-block">Program rate · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
