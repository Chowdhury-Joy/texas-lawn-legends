<section class="bg-white">
    <div class="mx-auto max-w-3xl px-6 py-16">
        <h2 data-field="heading" data-gjs-type="text" class="mb-6 text-3xl font-black uppercase tracking-tight text-slate-900 sm:text-4xl">
            {{ filled($data['heading'] ?? null) ? $data['heading'] : '[Rich Text Heading]' }}
        </h2>
        <div data-field="body" data-gjs-type="text" class="prose prose-slate max-w-none">
            {!! filled($data['body'] ?? null) ? $data['body'] : '[Write rich text contents here]' !!}
        </div>
    </div>
</section>
