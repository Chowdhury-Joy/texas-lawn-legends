@extends('layouts.app')

@section('content')

    @php
        $phone = setting('primary_phone', '(214) 617-7725');
        $telHref = 'tel:+1' . preg_replace('/\D/', '', (string) $phone);
        $areas = (array) setting('service_areas', []);
        $heroImage = setting_image('hero_media_image');
    @endphp

    {{-- ============================ HERO ============================ --}}
    <section class="border-b-4 border-slate-950 bg-brand-paper">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-16 lg:grid-cols-12">
            <div class="lg:col-span-7">
                @if ($eyebrow = setting('hero_eyebrow'))
                    <span class="mb-6 inline-block bg-emerald-900 px-3 py-1 text-xs font-bold uppercase tracking-widest text-yellow-400">{{ $eyebrow }}</span>
                @endif
                <h1 class="text-5xl font-extrabold leading-[1.05] tracking-tight text-slate-900 sm:text-6xl">
                    {{ setting('hero_heading', 'Transform Your Dallas Yard Into An Outdoor Retreat.') }}
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-slate-600">
                    {{ setting('hero_subheading') }}
                    @if (! empty($areas))
                        Serving {{ collect($areas)->join(', ', ', and ') }}, and premier Dallas neighborhoods.
                    @endif
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ url('/estimate') }}" class="btn-brutal bg-yellow-400 px-7 py-4 text-base text-slate-950">{{ setting('hero_cta_primary_label', 'Start Your Free Estimate') }}</a>
                    <a href="{{ $telHref }}" class="inline-flex items-center justify-center gap-2 border-4 border-slate-950 bg-transparent px-7 py-4 text-base font-black uppercase tracking-wide text-slate-900 transition-colors hover:bg-white">
                        {{ setting('hero_cta_secondary_label', 'Call or Text') }}: {{ $phone }}
                    </a>
                </div>
            </div>

            {{-- Right media box --}}
            <div class="lg:col-span-5">
                <div class="box-brutal relative overflow-hidden" style="box-shadow: 8px 8px 0px 0px rgba(27,67,50,1);">
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" alt="{{ setting('hero_media_title') }}" class="aspect-[4/5] w-full object-cover">
                    @else
                        <div class="aspect-[4/5] w-full bg-gradient-to-br from-emerald-800 via-emerald-900 to-slate-900">
                            <div class="flex h-full flex-col justify-between p-6">
                                <div class="flex items-center justify-between">
                                    <span class="bg-yellow-400 px-2 py-1 text-[10px] font-black uppercase tracking-widest text-slate-950">{{ setting('hero_media_badge', 'Featured Build') }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-200">{{ setting('hero_media_neighborhood', 'Kessler Park') }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 opacity-90">
                                    @for ($i = 0; $i < 9; $i++)
                                        <div class="aspect-square border-2 border-emerald-950/40 bg-emerald-700/40"></div>
                                    @endfor
                                </div>
                                <div>
                                    <p class="text-2xl font-black uppercase leading-tight text-white">{{ setting('hero_media_title', 'Full Yard Renovation') }}</p>
                                    <p class="mt-1 text-sm text-emerald-200">{{ setting('hero_media_subtitle') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= TRUST / VERIFICATION BAR ======================= --}}
    @php $trustBadges = (array) setting('trust_badges', []); @endphp
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

    {{-- ======================= 3-STEP BLUEPRINT ======================= --}}
    @php $processSteps = (array) setting('process_steps', []); @endphp
    <section id="about" class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-20">
            <h2 class="mx-auto max-w-3xl text-center text-4xl font-black uppercase leading-tight tracking-tight text-slate-900 sm:text-5xl">
                {{ setting('process_heading', 'Our 3-Step Transformation Process — Deliver The Wow') }}
            </h2>
            <div class="mt-14 grid grid-cols-1 gap-8 lg:grid-cols-3">
                @foreach ($processSteps as $step)
                    <div class="box-brutal p-8">
                        <span class="block text-6xl font-black leading-none text-yellow-400" style="-webkit-text-stroke: 2px #0f172a;">{{ $step['number'] ?? '' }}</span>
                        <h3 class="mt-4 text-xl font-black uppercase tracking-tight text-slate-900">{{ $step['title'] ?? '' }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $step['body'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================= SPLIT ARCHITECTURAL MATRIX ======================= --}}
    <section id="services" class="border-t-4 border-slate-950">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            {{-- CREATE SUITE --}}
            <div id="create-suite" class="border-b-4 border-slate-950 bg-emerald-900 px-6 py-16 text-white lg:border-b-0 lg:border-r-4">
                <div class="mx-auto max-w-xl lg:ml-auto lg:mr-0 lg:max-w-lg lg:pr-10">
                    <span class="inline-block bg-yellow-400 px-3 py-1 text-xs font-black uppercase tracking-widest text-slate-950">The Create Suite</span>
                    <h2 class="mt-5 text-3xl font-black uppercase leading-tight tracking-tight sm:text-4xl">{{ setting('create_suite_heading', 'Premium Landscape Design & Structural Hardscaping') }}</h2>
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
                    <h2 class="mt-5 text-3xl font-black uppercase leading-tight tracking-tight sm:text-4xl">{{ setting('care_suite_heading', 'Comprehensive Property Preservation & Lawn Maintenance') }}</h2>
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

    {{-- ======================= NEIGHBORHOOD PROOF ======================= --}}
    <section id="portfolio" class="border-t-4 border-slate-950 bg-brand-paper">
        <div class="mx-auto max-w-7xl px-6 py-20"
             x-data="{ active: 'All Neighborhoods' }">
            <h2 class="text-center text-4xl font-black uppercase tracking-tight text-slate-900 sm:text-5xl">{{ setting('proof_heading', 'Verified Local Proof') }}</h2>
            @if ($proofSub = setting('proof_subheading'))
                <p class="mx-auto mt-3 max-w-2xl text-center text-slate-600">{{ $proofSub }}</p>
            @endif

            <div class="mt-10 flex flex-wrap justify-center gap-2">
                @foreach (collect(['All Neighborhoods'])->merge($neighborhoods) as $tab)
                    <button type="button"
                            @click="active = @js($tab)"
                            :class="active === @js($tab) ? 'bg-slate-950 text-yellow-400' : 'bg-white text-slate-900 hover:bg-yellow-400'"
                            class="border-2 border-slate-950 px-4 py-2 text-xs font-black uppercase tracking-wide transition-colors">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>

            <div class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($testimonials as $review)
                    <div class="box-brutal flex flex-col"
                         :class="{ 'hidden': !(active === 'All Neighborhoods' || active === @js($review->neighborhood)) }">
                        <div class="grid grid-cols-2 border-b-2 border-slate-950">
                            <div class="flex aspect-square items-center justify-center border-r-2 border-slate-950 bg-slate-200">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-500">Before</span>
                            </div>
                            <div class="flex aspect-square items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-900">
                                <span class="text-xs font-black uppercase tracking-widest text-yellow-400">After</span>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="text-yellow-500">{!! str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating) !!}</div>
                            <blockquote class="mt-3 flex-1 text-sm leading-relaxed text-slate-700">“{{ $review->review_text }}”</blockquote>
                            <div class="mt-4 border-t-2 border-slate-200 pt-4">
                                <p class="text-sm font-black uppercase tracking-tight text-slate-900">{{ $review->author }}</p>
                                <p class="mt-1 inline-block bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                                    Completed in {{ $review->neighborhood }} — Verified Review
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-slate-500">Featured reviews will appear here soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ======================= STICKY CTA GATE ======================= --}}
    <section id="book" class="border-t-4 border-slate-950 bg-yellow-400 px-6 py-16 text-center">
        <div class="mx-auto max-w-3xl">
            <h2 class="text-3xl font-black uppercase leading-tight tracking-tight text-slate-950 sm:text-4xl">
                {{ setting('cta_heading', 'Ready To Systematize Your Property Transformation?') }}
            </h2>
            @if ($ctaSub = setting('cta_subheading'))
                <p class="mx-auto mt-4 max-w-xl text-base font-medium text-slate-800">{{ $ctaSub }}</p>
            @endif
            <a href="{{ url('/estimate') }}"
               class="mt-8 inline-block bg-slate-950 px-12 py-5 text-xl font-black uppercase tracking-wider text-yellow-400 transition-colors hover:bg-slate-900 shadow-brutal-forest">
                {{ setting('cta_button_label', 'Launch Instant Evaluation Engine') }}
            </a>
        </div>
    </section>

@endsection
