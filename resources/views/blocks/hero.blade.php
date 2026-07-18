@php
    $phone = setting('primary_phone', '(214) 617-7725');
    $telHref = 'tel:+1' . preg_replace('/\D/', '', (string) $phone);
    $areas = (array) setting('service_areas', []);
    $heroImage = filled($data['media_image'] ?? null) ? \Illuminate\Support\Facades\Storage::disk('public')->url($data['media_image']) : null;
@endphp

<section class="border-b-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-16 lg:grid-cols-12">
        <div class="lg:col-span-7">
            @if ($eyebrow = $data['eyebrow'] ?? null)
                <span data-field="eyebrow" class="mb-6 inline-block bg-emerald-900 px-3 py-1 text-xs font-bold uppercase tracking-widest text-yellow-400">{{ $eyebrow }}</span>
            @endif
            <h1 data-field="heading" class="text-5xl font-extrabold leading-[1.05] tracking-tight text-slate-900 sm:text-6xl">
                {{ $data['heading'] ?? 'Transform Your Dallas Yard Into An Outdoor Retreat.' }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-slate-600">
                <span data-field="subheading">{{ $data['subheading'] ?? '' }}</span>
                @if (! empty($areas))
                    Serving {{ collect($areas)->join(', ', ', and ') }}, and premier Dallas neighborhoods.
                @endif
            </p>
            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                <a href="{{ url('/estimate') }}" data-field="cta_primary_label" class="btn-brutal bg-yellow-400 px-7 py-4 text-base text-slate-950">{{ $data['cta_primary_label'] ?? 'Start Your Free Estimate' }}</a>
                <a href="{{ $telHref }}" class="inline-flex items-center justify-center gap-2 border-4 border-slate-950 bg-transparent px-7 py-4 text-base font-black uppercase tracking-wide text-slate-900 transition-colors hover:bg-white">
                    <span data-field="cta_secondary_label">{{ $data['cta_secondary_label'] ?? 'Call or Text' }}</span>: {{ $phone }}
                </a>
            </div>
        </div>

        {{-- Right media box --}}
        <div class="lg:col-span-5">
            <div class="box-brutal relative overflow-hidden" style="box-shadow: 8px 8px 0px 0px rgba(27,67,50,1);">
                @if ($heroImage)
                    <img src="{{ $heroImage }}" alt="{{ $data['media_title'] ?? '' }}" class="aspect-[4/5] w-full object-cover">
                @else
                    <div class="aspect-[4/5] w-full bg-gradient-to-br from-emerald-800 via-emerald-900 to-slate-900">
                        <div class="flex h-full flex-col justify-between p-6">
                            <div class="flex items-center justify-between">
                                <span data-field="media_badge" class="bg-yellow-400 px-2 py-1 text-[10px] font-black uppercase tracking-widest text-slate-950">{{ $data['media_badge'] ?? 'Featured Build' }}</span>
                                <span data-field="media_neighborhood" class="text-[10px] font-bold uppercase tracking-widest text-emerald-200">{{ $data['media_neighborhood'] ?? 'Kessler Park' }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 opacity-90">
                                @for ($i = 0; $i < 9; $i++)
                                    <div class="aspect-square border-2 border-emerald-950/40 bg-emerald-700/40"></div>
                                @endfor
                            </div>
                            <div>
                                <p data-field="media_title" class="text-2xl font-black uppercase leading-tight text-white">{{ $data['media_title'] ?? 'Full Yard Renovation' }}</p>
                                <p data-field="media_subtitle" class="mt-1 text-sm text-emerald-200">{{ $data['media_subtitle'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
