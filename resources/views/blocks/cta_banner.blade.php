@php
    /*
     * Merge strategy — same safe pattern as hero.blade.php:
     * Repeater entries render first (in drag order), then any classic fields
     * not already covered are appended so partial population is never destructive.
     */
    $repeaterElements = $data['text_elements'] ?? [];
    $handledTypes     = array_column($repeaterElements, 'type');

    $classicFallbacks = [];

    if (! in_array('eyebrow', $handledTypes) && filled($data['eyebrow'] ?? null)) {
        $classicFallbacks[] = ['type' => 'eyebrow', 'text' => $data['eyebrow']];
    }

    if (! in_array('heading', $handledTypes) && (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))) {
        $classicFallbacks[] = ['type' => 'heading', 'text' => $data['heading'] ?? null];
    }

    if (! in_array('subheading', $handledTypes) && filled($data['subheading'] ?? null)) {
        $classicFallbacks[] = ['type' => 'subheading', 'text' => $data['subheading']];
    }

    // Classic button_label field
    if (! in_array('button', $handledTypes) && ! in_array('primary_cta', $handledTypes) && filled($data['button_label'] ?? null)) {
        $classicFallbacks[] = [
            'type' => 'button',
            'text' => $data['button_label'],
            'url'  => $data['button_url'] ?? null,
        ];
    }

    $renderElements = array_merge($repeaterElements, $classicFallbacks);

    // Shared button URL from the standalone button_url field (used when repeater entry has no url)
    $defaultButtonUrl = filled($data['button_url'] ?? null) ? $data['button_url'] : url('/estimate');
@endphp

<section class="cta-banner bg-yellow-400 px-6 py-16 text-center">
    <div class="mx-auto max-w-3xl">
        @foreach ($renderElements as $element)
            @php
                if ($element['is_hidden'] ?? false) {
                    continue;
                }
                $type = $element['type'] ?? '';
                $text = $element['text'] ?? null;
                $url  = filled($element['url'] ?? null) ? $element['url'] : $defaultButtonUrl;
            @endphp

            @if ($type === 'eyebrow' && filled($text))
                <span data-field="eyebrow" class="mb-3 inline-block w-fit bg-slate-950 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                    {{ $text }}
                </span>

            @elseif ($type === 'heading')
                <h2 data-field="heading" class="text-3xl font-medium leading-tight tracking-tighter text-slate-950 sm:text-4xl">
                    {{ filled($text) ? $text : 'Ready To Systematize Your Property Transformation?' }}
                </h2>

            @elseif ($type === 'subheading' && filled($text))
                <p data-field="subheading" class="mx-auto mt-4 max-w-xl text-base font-medium text-slate-800">{{ $text }}</p>

            @elseif (($type === 'button' || $type === 'primary_cta') && filled($text))
                <a href="{{ $url }}"
                   @click.prevent="$dispatch('open-estimate-modal')"
                   data-field="button_label"
                   class="mt-8 inline-block bg-slate-950 px-12 py-5 text-xl font-medium uppercase tracking-wider text-yellow-400 transition-colors hover:bg-slate-900 shadow-brutal-forest">
                    {{ $text }}
                </a>
            @endif
        @endforeach

        <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-xs font-bold text-slate-900">
            <span>⭐ Verified 5-Star Local Service</span>
            <span>·</span>
            <span>No-Obligation 30-Min Consultation</span>
        </div>
    </div>
</section>
