@php $isFirst = (bool) ($data['is_first_block'] ?? false); @endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div @class(['mx-auto space-section', 'max-w-3xl text-center' => $isFirst, 'max-w-3xl' => ! $isFirst])>
        @if (filled($data['heading'] ?? null))
            @if ($isFirst)
                <h1 data-field="heading" data-reveal class="type-h1 text-slate-900">
                    {{ $data['heading'] }}
                </h1>
            @else
                <h2 data-field="heading" data-reveal class="type-h2 mb-[var(--space-lg)] text-slate-900">
                    {{ $data['heading'] }}
                </h2>
            @endif
        @endif
        @if (filled($data['body'] ?? null))
            <div data-field="body" data-reveal @class(['prose prose-slate type-body-lg max-w-none', 'mt-[var(--space-md)] mx-auto' => $isFirst])>
                {!! $data['body'] !!}
            </div>
        @endif
    </div>
</section>
