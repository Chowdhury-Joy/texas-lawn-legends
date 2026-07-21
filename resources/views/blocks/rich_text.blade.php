@php $isFirst = (bool) ($data['is_first_block'] ?? false); @endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div @class(['mx-auto px-6 py-section', 'max-w-3xl text-center' => $isFirst, 'max-w-3xl' => ! $isFirst])>
        @if (filled($data['heading'] ?? null))
            @if ($isFirst)
                <h1 data-field="heading" data-reveal class="text-4xl font-medium tracking-tighter text-slate-900 sm:text-5xl">
                    {{ $data['heading'] }}
                </h1>
            @else
                <h2 data-field="heading" data-reveal class="mb-6 text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                    {{ $data['heading'] }}
                </h2>
            @endif
        @endif
        @if (filled($data['body'] ?? null))
            <div data-field="body" data-reveal @class(['prose prose-slate max-w-none', 'mt-5 mx-auto text-lg' => $isFirst])>
                {!! $data['body'] !!}
            </div>
        @endif
    </div>
</section>
