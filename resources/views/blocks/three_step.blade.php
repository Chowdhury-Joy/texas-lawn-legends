@php $processSteps = (array) ($data['steps'] ?? []); @endphp
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-6 py-20">
        <h2 data-field="heading" data-gjs-type="text" class="mx-auto max-w-3xl text-center text-4xl font-medium leading-tight tracking-tighter text-slate-900 sm:text-5xl">
            {{ filled($data['heading'] ?? null) ? $data['heading'] : '[Our 3-Step Process — Deliver The Wow]' }}
        </h2>
        <div class="mt-14 grid grid-cols-1 gap-8 lg:grid-cols-3">
            @foreach ($processSteps as $index => $step)
                <div class="box-brutal p-8 mr-[8px] lg:mr-0">
                    <span data-field="steps.{{ $index }}.number" data-gjs-type="text" class="block text-6xl font-black leading-none text-yellow-400" style="-webkit-text-stroke: 2px #0f172a;">
                        {{ filled($step['number'] ?? null) ? $step['number'] : '[' . ($index + 1) . ']' }}
                    </span>
                    <h3 data-field="steps.{{ $index }}.title" data-gjs-type="text" class="mt-4 text-xl font-medium tracking-tighter text-slate-900">
                        {{ filled($step['title'] ?? null) ? $step['title'] : '[Step Title]' }}
                    </h3>
                    <p data-field="steps.{{ $index }}.body" data-gjs-type="text" class="mt-3 text-sm leading-relaxed text-slate-600">
                        {{ filled($step['body'] ?? null) ? $step['body'] : '[Step Description]' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
