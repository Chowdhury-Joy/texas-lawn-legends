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

<section class="cta-banner bg-yellow-400 space-section text-center text-on-accent">
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
                <span data-field="eyebrow" data-stagger style="--stagger-i: {{ $loop->index }}" class="chip-on-accent type-tagline mb-3 inline-block w-fit px-2.5 py-1">
                    {{ $text }}
                </span>

            @elseif ($type === 'heading')
                <h2 data-field="heading" data-stagger style="--stagger-i: {{ $loop->index }}" class="type-h2 text-on-accent">
                    {{ filled($text) ? $text : 'Ready To Systematize Your Property Transformation?' }}
                </h2>

            @elseif ($type === 'subheading' && filled($text))
                <p data-field="subheading" data-stagger style="--stagger-i: {{ $loop->index }}" class="type-body-md mx-auto mt-4 max-w-xl text-on-accent/80">{{ $text }}</p>

            @elseif (($type === 'button' || $type === 'primary_cta') && filled($text))
                <a href="{{ $url }}"
                   @if (product_part_at_least(2)) @click.prevent="$dispatch('open-estimate-modal')" @endif
                   data-field="button_label"
                   data-stagger style="--stagger-i: {{ $loop->index }}"
                   class="chip-on-accent type-btn mt-8 inline-block px-12 py-5 transition-colors shadow-brutal-forest">
                    {{ $text }}
                </a>
            @endif
        @endforeach

        <div data-reveal class="type-tagline mt-4 flex flex-wrap items-center justify-center gap-2 text-on-accent">
            <span>⭐ Verified 5-Star Local Service</span>
            <span>·</span>
            <span>No-Obligation 30-Min Consultation</span>
        </div>
    </div>
</section>
