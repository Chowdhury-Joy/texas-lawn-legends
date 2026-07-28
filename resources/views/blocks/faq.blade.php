@php $items = (array) ($data['items'] ?? []); @endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-3xl space-section">
        @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
            <h2 data-field="heading" data-reveal class="type-h2 mb-[var(--space-xxl)] text-center text-slate-900">
                {{ filled($data['heading'] ?? null) ? $data['heading'] : 'Frequently Asked Questions' }}
            </h2>
        @endif
        <div class="stack-list" x-data="{ open: null }">
            @foreach ($items as $index => $item)
                @if (filled($item['question'] ?? null))
                    <div data-stagger style="--stagger-i: {{ $index }}" class="box-brutal mr-[8px]">
                        <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                                class="type-h4 flex w-full items-center justify-between space-card text-left text-slate-900">
                            <span data-field="items.{{ $index }}.question">
                                {{ $item['question'] }}
                            </span>
                            <span x-text="open === {{ $index }} ? '−' : '+'" class="ml-4 type-h3"></span>
                        </button>
                        @if (filled($item['answer'] ?? null))
                            <div x-show="open === {{ $index }}" x-cloak class="type-body-md border-t-2 border-slate-950 space-card text-slate-600">
                                <span data-field="items.{{ $index }}.answer">
                                    {{ $item['answer'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
