@php
    $phone = setting('primary_phone', '(214) 617-7725');
    $telHref = 'tel:+1' . preg_replace('/\D/', '', (string) $phone);

    $heroImage = filled($data['media_image'] ?? null)
        ? public_url($data['media_image'])
        : asset('images/hero_desktop.jpg');

    $heroImageMobile = filled($data['media_image_mobile'] ?? null)
        ? public_url($data['media_image_mobile'])
        : (filled($data['media_image'] ?? null)
            ? public_url($data['media_image'])
            : asset('images/hero_mobile.jpg'));

    /*
     * Merge strategy — safe against partial repeater population:
     *
     * 1. Start with whatever the editor put in the draggable repeater (custom order + custom text).
     * 2. For each classic field that is NOT already covered by a repeater entry, append it at the
     *    end so existing content is never silently dropped.
     * 3. If the repeater is completely empty, only the classic fields are rendered — i.e., all
     *    pre-existing blocks are completely unaffected.
     *
     * This means "adding one eyebrow entry to the repeater" keeps heading/subheading/CTAs visible.
     */
    $repeaterElements = $data['text_elements'] ?? [];
    $handledTypes     = array_column($repeaterElements, 'type');

    $classicFallbacks = [];

    if (! in_array('eyebrow', $handledTypes) && filled($data['eyebrow'] ?? null)) {
        $classicFallbacks[] = ['type' => 'eyebrow', 'text' => $data['eyebrow']];
    }

    $headingMissing = ! in_array('heading', $handledTypes);
    if ($headingMissing && (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))) {
        $classicFallbacks[] = [
            'type' => 'heading',
            'text' => $data['heading'] ?? null, // null triggers default fallback text in renderer below
        ];
    }

    if (! in_array('subheading', $handledTypes) && (! array_key_exists('subheading', $data) || filled($data['subheading'] ?? null))) {
        $classicFallbacks[] = [
            'type' => 'subheading',
            'text' => $data['subheading'] ?? null,
        ];
    }

    if (! in_array('primary_cta', $handledTypes) && (! array_key_exists('cta_primary_label', $data) || filled($data['cta_primary_label'] ?? null))) {
        $classicFallbacks[] = [
            'type' => 'primary_cta',
            'text' => $data['cta_primary_label'] ?? null,
        ];
    }

    if (! in_array('secondary_cta', $handledTypes) && (! array_key_exists('cta_secondary_label', $data) || filled($data['cta_secondary_label'] ?? null))) {
        $classicFallbacks[] = [
            'type' => 'secondary_cta',
            'text' => $data['cta_secondary_label'] ?? null,
        ];
    }

    // Final ordered list: repeater entries first (custom order), then any remaining classic fields.
    $renderElements = array_merge($repeaterElements, $classicFallbacks);
@endphp

<section class="border-b-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-16 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-7">
            {{-- Micro-Social Proof Trust Badge (Static) --}}
            <div class="mb-4 flex items-center gap-1.5 text-sm font-bold text-slate-700">
                <span class="inline-flex items-center text-yellow-500 font-black">★★★★★</span>
                <span>4.9/5 Rating (140+ Dallas Homeowners)</span>
            </div>

            @foreach ($renderElements as $element)
                @php
                    if ($element['is_hidden'] ?? false) {
                        continue;
                    }
                    $type = $element['type'] ?? '';
                    // Repeater entry text takes priority; for classic-field appended entries the
                    // text may be null — each branch handles its own default below.
                    $text = $element['text'] ?? null;
                @endphp

                @if ($type === 'eyebrow' && filled($text))
                    <span data-field="eyebrow" class="mb-4 inline-block w-fit bg-emerald-900 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                        {{ $text }}
                    </span>

                @elseif ($type === 'heading')
                    <h1 data-field="heading" class="text-4xl font-medium leading-[1.05] tracking-tighter text-slate-900 sm:text-5xl lg:text-6xl">
                        {{ filled($text) ? $text : 'Transform Your Dallas Yard Into An Outdoor Retreat.' }}
                    </h1>

                @elseif ($type === 'subheading')
                    <p class="mt-6 max-w-2xl text-lg leading-relaxed text-slate-600">
                        <span data-field="subheading">{{ filled($text) ? $text : 'Professional design, precision hardscaping, and premier maintenance you can actually rely on.' }}</span>
                    </p>

                @elseif ($type === 'primary_cta')
                    {{-- Unified Location-Based Instant Pricing CTA Form --}}
                    <form action="{{ url('/estimate') }}" method="GET" class="mt-8 max-w-xl"
                          x-data="{ location: '' }"
                          @submit.prevent="$dispatch('open-estimate-modal', { location: location.trim() })">
                        <label class="mb-1.5 block text-xs font-black uppercase tracking-widest text-slate-700">View Instant Property Valuation</label>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input type="text"
                                   x-model="location"
                                   placeholder="Enter Dallas ZIP code or neighborhood..."
                                   class="w-full border-4 border-slate-950 bg-white px-4 py-3 text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 sm:py-4">
                            <button type="submit" class="btn-brutal flex shrink-0 items-center justify-center bg-yellow-400 px-6 py-3 text-sm text-slate-950 sm:px-8 sm:py-4 sm:text-base">
                                {{ filled($text) ? $text : 'View Instant Pricing →' }}
                            </button>
                        </div>
                    </form>

                @elseif ($type === 'secondary_cta')
                    <div class="mt-4 flex flex-col items-start gap-4">
                        <a href="{{ $telHref }}" class="inline-flex items-center justify-center gap-2 border-2 border-slate-950 bg-white px-5 py-2.5 text-xs font-black uppercase tracking-wide text-slate-900 transition-colors hover:bg-slate-100">
                            <span data-field="cta_secondary_label">{{ filled($text) ? $text : 'Call or Text' }}</span>
                            <span>: {{ $phone }}</span>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Right media box --}}
        <div class="lg:col-span-5 mr-[8px] lg:mr-0">
            <div class="box-brutal relative overflow-hidden">
                <picture>
                    <source srcset="{{ $heroImageMobile }}" media="(max-width: 631px)">
                    <img src="{{ $heroImage }}" alt="{{ $data['media_title'] ?? 'Texas Lawn Legends Residential Landscaping' }}" loading="lazy" decoding="async" class="aspect-square w-full object-cover">
                </picture>

                <!-- Gradient overlay for text readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-slate-950/40 flex flex-col justify-between p-6">
                    <div class="flex items-center justify-between">
                        @if (filled($data['media_badge'] ?? null) || ! array_key_exists('media_badge', $data))
                            <span data-field="media_badge" class="bg-yellow-400 px-2 py-1 text-[10px] font-black uppercase tracking-widest text-slate-950">
                                {{ filled($data['media_badge'] ?? null) ? $data['media_badge'] : 'Featured Build' }}
                            </span>
                        @endif
                        @if (filled($data['media_neighborhood'] ?? null) || ! array_key_exists('media_neighborhood', $data))
                            <span data-field="media_neighborhood" class="text-[10px] font-bold uppercase tracking-widest text-yellow-400 bg-slate-950/60 px-2 py-1">
                                {{ filled($data['media_neighborhood'] ?? null) ? $data['media_neighborhood'] : 'Kessler Park' }}
                            </span>
                        @endif
                    </div>
                    <div>
                        @if (filled($data['media_title'] ?? null) || ! array_key_exists('media_title', $data))
                            <p data-field="media_title" class="text-2xl font-medium leading-tight tracking-tighter text-white drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
                                {{ filled($data['media_title'] ?? null) ? $data['media_title'] : 'Full Yard Renovation' }}
                            </p>
                        @endif
                        @if (filled($data['media_subtitle'] ?? null) || ! array_key_exists('media_subtitle', $data))
                            <p data-field="media_subtitle" class="mt-1 text-sm font-semibold text-yellow-300 drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)]">
                                {{ filled($data['media_subtitle'] ?? null) ? $data['media_subtitle'] : 'Retaining walls · Flagstone patio · New sod' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
