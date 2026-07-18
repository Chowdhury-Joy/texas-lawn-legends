<section class="bg-white">
    <div class="mx-auto max-w-3xl px-6 py-16">
        @if ($heading = $data['heading'] ?? null)
            <h2 class="mb-6 text-3xl font-black uppercase tracking-tight text-slate-900 sm:text-4xl">{{ $heading }}</h2>
        @endif
        <div class="prose prose-slate max-w-none">
            {!! $data['body'] ?? '' !!}
        </div>
    </div>
</section>
