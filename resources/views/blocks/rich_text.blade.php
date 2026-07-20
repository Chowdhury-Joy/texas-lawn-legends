<section class="border-t-4 border-slate-950 bg-white">
    <div class="mx-auto max-w-3xl px-6 py-16">
        <h2 data-field="heading" class="mb-6 text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
            {{ filled($data['heading'] ?? null) ? $data['heading'] : '[Rich Text Heading]' }}
        </h2>
        <div data-field="body" class="prose prose-slate max-w-none">
            {!! filled($data['body'] ?? null) ? $data['body'] : '[Write rich text contents here]' !!}
        </div>
    </div>
</section>
