<section class="border-t-4 border-slate-950 bg-yellow-400 px-6 py-16 text-center">
    <div class="mx-auto max-w-3xl">
        <h2 data-field="heading" class="text-3xl font-medium leading-tight tracking-tighter text-slate-950 sm:text-4xl">
            {{ $data['heading'] ?? 'Ready To Systematize Your Property Transformation?' }}
        </h2>
        @if ($ctaSub = $data['subheading'] ?? null)
            <p data-field="subheading" class="mx-auto mt-4 max-w-xl text-base font-medium text-slate-800">{{ $ctaSub }}</p>
        @endif
        <a href="{{ filled($data['button_url'] ?? null) ? $data['button_url'] : url('/estimate') }}"
           data-field="button_label"
           class="mt-8 inline-block bg-slate-950 px-12 py-5 text-xl font-medium uppercase tracking-wider text-yellow-400 transition-colors hover:bg-slate-900 shadow-brutal-forest">
            {{ $data['button_label'] ?? 'Launch Instant Evaluation Engine' }}
        </a>
    </div>
</section>
