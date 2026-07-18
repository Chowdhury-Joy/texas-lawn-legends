<section id="services" class="border-t-4 border-slate-950">
    <div class="grid grid-cols-1 lg:grid-cols-2">
        {{-- CREATE SUITE --}}
        <div id="create-suite" class="border-b-4 border-slate-950 bg-emerald-900 px-6 py-16 text-white lg:border-b-0 lg:border-r-4">
            <div class="mx-auto max-w-xl lg:ml-auto lg:mr-0 lg:max-w-lg lg:pr-10">
                <span class="inline-block bg-yellow-400 px-3 py-1 text-xs font-black uppercase tracking-widest text-slate-950">The Create Suite</span>
                <h2 class="mt-5 text-3xl font-black uppercase leading-tight tracking-tight sm:text-4xl">{{ $data['create_suite_heading'] ?? 'Premium Landscape Design & Structural Hardscaping' }}</h2>
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($createServices as $service)
                        <div class="border-2 border-emerald-950/50 bg-emerald-800/40 p-5">
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-yellow-400" />
                            <h3 class="mt-3 text-base font-black uppercase tracking-tight text-white">{{ $service->title }}</h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-emerald-100">{{ $service->short_description }}</p>
                            <p class="mt-3 text-[11px] font-bold uppercase tracking-widest text-yellow-400">Custom pricing · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CARE SUITE --}}
        <div id="care-suite" class="bg-slate-800 px-6 py-16 text-white">
            <div class="mx-auto max-w-xl lg:mr-auto lg:ml-0 lg:max-w-lg lg:pl-10">
                <span class="inline-block bg-yellow-400 px-3 py-1 text-xs font-black uppercase tracking-widest text-slate-950">The Care Suite</span>
                <h2 class="mt-5 text-3xl font-black uppercase leading-tight tracking-tight sm:text-4xl">{{ $data['care_suite_heading'] ?? 'Comprehensive Property Preservation & Lawn Maintenance' }}</h2>
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($careServices as $service)
                        <div class="border-2 border-slate-950/60 bg-slate-900/50 p-5">
                            <x-svg-icon :name="$service->icon" class="h-8 w-8 text-yellow-400" />
                            <h3 class="mt-3 text-base font-black uppercase tracking-tight text-white">{{ $service->title }}</h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-slate-300">{{ $service->short_description }}</p>
                            <p class="mt-3 text-[11px] font-bold uppercase tracking-widest text-yellow-400">Program rate · ×{{ number_format($service->base_price_multiplier, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
