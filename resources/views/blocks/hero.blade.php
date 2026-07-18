@php
    $phone = setting('primary_phone', '(214) 617-7725');
    $telHref = 'tel:+1' . preg_replace('/\D/', '', (string) $phone);
    $areas = (array) setting('service_areas', []);
    
    $heroImage = filled($data['media_image'] ?? null) 
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($data['media_image']) 
        : asset('images/hero_desktop.jpg');
        
    $heroImageMobile = filled($data['media_image_mobile'] ?? null) 
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($data['media_image_mobile']) 
        : (filled($data['media_image'] ?? null) 
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($data['media_image']) 
            : asset('images/hero_mobile.jpg'));
@endphp

<section class="border-b-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-16 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-7">
            <h1 data-field="heading" class="text-4xl font-medium leading-[1.05] tracking-tighter text-slate-900 sm:text-5xl lg:text-6xl">
                {{ filled($data['heading'] ?? null) ? $data['heading'] : 'Transform Your Dallas Yard Into An Outdoor Retreat.' }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-slate-600">
                <span data-field="subheading">{{ filled($data['subheading'] ?? null) ? $data['subheading'] : 'Professional design, precision hardscaping, and premier maintenance you can actually rely on.' }}</span>
                @if (! empty($areas))
                    Serving {{ collect($areas)->join(', ', ', and ') }}, and premier Dallas neighborhoods.
                @endif
            </p>
            <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:flex-wrap lg:flex-nowrap">
                <a href="{{ url('/estimate') }}" data-field="cta_primary_label" class="btn-brutal bg-yellow-400 px-5 sm:px-7 py-3 sm:py-4 text-sm sm:text-base text-slate-950 flex items-center justify-center">
                    {{ filled($data['cta_primary_label'] ?? null) ? $data['cta_primary_label'] : 'Start Your Free Estimate' }}
                </a>
                <a href="{{ $telHref }}" class="inline-flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 border-4 border-slate-950 bg-transparent px-5 sm:px-7 py-3 sm:py-4 text-sm sm:text-base font-medium uppercase tracking-wide text-slate-900 transition-colors hover:bg-white">
                    <span data-field="cta_secondary_label">{{ filled($data['cta_secondary_label'] ?? null) ? $data['cta_secondary_label'] : 'Call or Text' }}</span>
                    <span class="hidden sm:inline">:</span>
                    <span>{{ $phone }}</span>
                </a>
            </div>
        </div>

        {{-- Right media box --}}
        <div class="lg:col-span-5 mr-[8px] lg:mr-0">
            <div class="box-brutal relative overflow-hidden">
                <picture>
                    <source srcset="{{ $heroImageMobile }}" media="(max-width: 1023px)">
                    <img src="{{ $heroImage }}" alt="{{ $data['media_title'] ?? 'Texas Lawn Legends Residential Landscaping' }}" class="w-full object-cover aspect-[16/9] lg:aspect-[4/5]">
                </picture>

                <!-- Gradient overlay for text readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-slate-950/40 flex flex-col justify-between p-6">
                    <div class="flex items-center justify-between">
                        <span data-field="media_badge" class="bg-yellow-400 px-2 py-1 text-[10px] font-black uppercase tracking-widest text-slate-950">
                            {{ filled($data['media_badge'] ?? null) ? $data['media_badge'] : 'Featured Build' }}
                        </span>
                        <span data-field="media_neighborhood" class="text-[10px] font-bold uppercase tracking-widest text-yellow-400 bg-slate-950/60 px-2 py-1">
                            {{ filled($data['media_neighborhood'] ?? null) ? $data['media_neighborhood'] : 'Kessler Park' }}
                        </span>
                    </div>
                    <div>
                        <p data-field="media_title" class="text-2xl font-medium leading-tight tracking-tighter text-white drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
                            {{ filled($data['media_title'] ?? null) ? $data['media_title'] : 'Full Yard Renovation' }}
                        </p>
                        <p data-field="media_subtitle" class="mt-1 text-sm font-semibold text-yellow-300 drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)]">
                            {{ filled($data['media_subtitle'] ?? null) ? $data['media_subtitle'] : 'Retaining walls · Flagstone patio · New sod' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
