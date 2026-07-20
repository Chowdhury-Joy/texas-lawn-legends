@php $items = (array) ($data['items'] ?? []); @endphp
<section class="border-t-4 border-slate-950 bg-slate-100">
    <div class="mx-auto max-w-3xl px-6 py-16">
        @if (! array_key_exists('heading', $data) || filled($data['heading'] ?? null))
            <h2 data-field="heading" class="mb-10 text-center text-3xl font-medium tracking-tighter text-slate-900 sm:text-4xl">
                {{ filled($data['heading'] ?? null) ? $data['heading'] : 'Frequently Asked Questions' }}
            </h2>
        @endif
        <div class="space-y-4" x-data="{ open: null }">
            @foreach ($items as $index => $item)
                @if (filled($item['question'] ?? null))
                    <div class="box-brutal mr-[8px]">
                        <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                                class="flex w-full items-center justify-between p-5 text-left text-base font-medium tracking-tighter text-slate-900">
                            <span data-field="items.{{ $index }}.question">
                                {{ $item['question'] }}
                            </span>
                            <span x-text="open === {{ $index }} ? '−' : '+'" class="ml-4 text-xl"></span>
                        </button>
                        @if (filled($item['answer'] ?? null))
                            <div x-show="open === {{ $index }}" x-cloak class="border-t-2 border-slate-950 p-5 text-sm leading-relaxed text-slate-600">
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
