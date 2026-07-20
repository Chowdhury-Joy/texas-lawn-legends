<section class="cta-banner bg-yellow-400 px-6 py-16 text-center">
    <div class="mx-auto max-w-3xl">
        @if (filled($data['text_elements'] ?? null))
            @foreach ($data['text_elements'] as $element)
                @php
                    $type = $element['type'] ?? '';
                    $text = $element['text'] ?? '';
                    $url = $element['url'] ?? null;
                @endphp

                @if ($type === 'eyebrow' && filled($text))
                    <span data-field="eyebrow" class="mb-3 inline-block w-fit bg-slate-950 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                        {{ $text }}
                    </span>
                @elseif ($type === 'heading' && filled($text))
                    <h2 data-field="heading" class="mb-4 text-3xl font-medium leading-tight tracking-tighter text-slate-950 sm:text-4xl">
                        {{ $text }}
                    </h2>
                @elseif ($type === 'subheading' && filled($text))
                    <p data-field="subheading" class="mx-auto mb-6 max-w-xl text-base font-medium text-slate-800">{{ $text }}</p>
                @elseif (($type === 'button' || $type === 'primary_cta') && filled($text))
                    <a href="{{ filled($url) ? $url : url('/estimate') }}"
                       @click.prevent="$dispatch('open-estimate-modal')"
                       data-field="button_label"
                       class="my-4 inline-block bg-slate-950 px-12 py-5 text-xl font-medium uppercase tracking-wider text-yellow-400 transition-colors hover:bg-slate-900 shadow-brutal-forest">
                        {{ $text }}
                    </a>
                @endif
            @endforeach
        @else
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" class="mb-3 inline-block w-fit bg-slate-950 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif
            @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                <h2 data-field="heading" class="text-3xl font-medium leading-tight tracking-tighter text-slate-950 sm:text-4xl">
                    {{ filled($data['heading'] ?? null) ? $data['heading'] : 'Ready To Systematize Your Property Transformation?' }}
                </h2>
            @endif
            @if (filled($data['subheading'] ?? null))
                <p data-field="subheading" class="mx-auto mt-4 max-w-xl text-base font-medium text-slate-800">{{ $data['subheading'] }}</p>
            @endif
            @if (filled($data['button_label'] ?? null))
                <a href="{{ filled($data['button_url'] ?? null) ? $data['button_url'] : url('/estimate') }}"
                   @click.prevent="$dispatch('open-estimate-modal')"
                   data-field="button_label"
                   class="mt-8 inline-block bg-slate-950 px-12 py-5 text-xl font-medium uppercase tracking-wider text-yellow-400 transition-colors hover:bg-slate-900 shadow-brutal-forest">
                    {{ $data['button_label'] }}
                </a>
            @endif
        @endif
        <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-xs font-bold text-slate-900">
            <span>⭐ Verified 5-Star Local Service</span>
            <span>·</span>
            <span>No-Obligation 30-Min Consultation</span>
        </div>
    </div>
</section>
