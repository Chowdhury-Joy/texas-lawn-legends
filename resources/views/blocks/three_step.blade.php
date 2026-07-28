@php
    $processSteps = (array) ($data['steps'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);

    /*
     * Merge strategy — same safe pattern as hero/cta_banner:
     * Repeater entries render in drag order, then classic fields not already
     * covered are appended — so adding one repeater entry never drops the rest.
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

    $renderElements = array_merge($repeaterElements, $classicFallbacks);
@endphp
<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto max-w-7xl space-section text-center">
        @foreach ($renderElements as $element)
            @php
                if ($element['is_hidden'] ?? false) {
                    continue;
                }
                $type = $element['type'] ?? '';
                $text = $element['text'] ?? null;
            @endphp

            @if ($type === 'eyebrow' && filled($text))
                <span data-field="eyebrow" data-stagger style="--stagger-i: {{ $loop->index }}" class="type-tagline mx-auto mb-3 inline-block w-fit bg-emerald-900 px-2 py-0.5 text-yellow-400">
                    {{ $text }}
                </span>

            @elseif ($type === 'heading')
                <h2 data-field="heading" data-stagger style="--stagger-i: {{ $loop->index }}" class="type-h2 mx-auto max-w-3xl text-slate-900">
                    {{ filled($text) ? $text : 'Our 3-Step Process — Deliver The Wow' }}
                </h2>

            @elseif ($type === 'subheading' && filled($text))
                <p data-field="subheading" data-stagger style="--stagger-i: {{ $loop->index }}" class="type-body-md mx-auto mt-4 max-w-2xl text-slate-600">
                    {{ $text }}
                </p>
            @endif
        @endforeach
        <div class="stack-after-header text-left {{ $layout ?: 'grid-steps' }}">
            @foreach ($processSteps as $index => $step)
                @php
                    $rawNumber = filled($step['number'] ?? null) ? (string) $step['number'] : '0'.($index + 1);
                    // Keep digits only — drop decorative stars/emoji that sometimes land in CMS numbers.
                    $stepNumber = preg_replace('/[^\dA-Za-z]/u', '', $rawNumber) ?: '0'.($index + 1);
                @endphp
                <div data-stagger style="--stagger-i: {{ $index }}" class="box-brutal space-card mr-[8px] desk:mr-0">
                    <span data-field="steps.{{ $index }}.number" class="type-h1 block text-[#1a1a1a]">
                        {{ $stepNumber }}
                    </span>
                    @if (filled($step['title'] ?? null))
                        <h3 data-field="steps.{{ $index }}.title" class="type-h4 mt-4 text-slate-900">
                            {{ $step['title'] }}
                        </h3>
                    @endif
                    @if (filled($step['body'] ?? null))
                        <p data-field="steps.{{ $index }}.body" class="type-body-sm mt-3 text-slate-600">
                            {{ $step['body'] }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
