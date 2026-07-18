@php $processSteps = (array) ($data['steps'] ?? []); @endphp
<section id="about" class="bg-white">
    <div class="mx-auto max-w-7xl px-6 py-20">
        <h2 data-field="heading" class="mx-auto max-w-3xl text-center text-4xl font-black uppercase leading-tight tracking-tight text-slate-900 sm:text-5xl">
            {{ $data['heading'] ?? 'Our 3-Step Transformation Process — Deliver The Wow' }}
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
