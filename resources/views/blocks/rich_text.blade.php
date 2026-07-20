<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto max-w-3xl px-6 py-16">
        @if (filled($data['heading'] ?? null))
            <h2 data-field="heading" class="mb-6 text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                {{ $data['heading'] }}
            </h2>
        @endif
        @if (filled($data['body'] ?? null))
            <div data-field="body" class="prose prose-slate max-w-none">
                {!! $data['body'] !!}
            </div>
        @endif
    </div>
</section>
