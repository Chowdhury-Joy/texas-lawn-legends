@php
    $processSteps = (array) ($data['steps'] ?? []);
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
@endphp
<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-20 text-center">
        @if (filled($data['text_elements'] ?? null))
            @foreach ($data['text_elements'] as $element)
                @php
                    $type = $element['type'] ?? '';
                    $text = $element['text'] ?? '';
                @endphp

                @if ($type === 'eyebrow' && filled($text))
                    <span data-field="eyebrow" class="mx-auto mb-3 inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                        {{ $text }}
                    </span>
                @elseif ($type === 'heading' && filled($text))
                    <h2 data-field="heading" class="mx-auto mb-4 max-w-3xl text-4xl font-medium leading-tight tracking-tighter text-slate-900 sm:text-5xl">
                        {{ $text }}
                    </h2>
                @elseif ($type === 'subheading' && filled($text))
                    <p data-field="subheading" class="mx-auto mb-4 max-w-2xl text-base text-slate-600">
                        {{ $text }}
                    </p>
                @endif
            @endforeach
        @else
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" class="mx-auto mb-3 inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif
            @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
                <h2 data-field="heading" class="mx-auto max-w-3xl text-4xl font-medium leading-tight tracking-tighter text-slate-900 sm:text-5xl">
                    {{ filled($data['heading'] ?? null) ? $data['heading'] : 'Our 3-Step Process — Deliver The Wow' }}
                </h2>
            @endif
        @endif
        <div class="mt-14 text-left {{ $layout ?: 'grid grid-cols-1 gap-8 lg:grid-cols-3' }}">
            @foreach ($processSteps as $index => $step)
                <div class="box-brutal p-5 sm:p-8 mr-[8px] lg:mr-0">
                    <span data-field="steps.{{ $index }}.number" class="block text-6xl font-black leading-none text-yellow-400" style="-webkit-text-stroke: 2px #0f172a;">
                        {{ filled($step['number'] ?? null) ? $step['number'] : '0' . ($index + 1) }}
                    </span>
                    @if (filled($step['title'] ?? null))
                        <h3 data-field="steps.{{ $index }}.title" class="mt-4 text-2xl font-medium tracking-tighter text-slate-900">
                            {{ $step['title'] }}
                        </h3>
                    @endif
                    @if (filled($step['body'] ?? null))
                        <p data-field="steps.{{ $index }}.body" class="mt-3 text-sm leading-relaxed text-slate-600">
                            {{ $step['body'] }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
