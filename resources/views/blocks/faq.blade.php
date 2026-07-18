@php $items = (array) ($data['items'] ?? []); @endphp
@if (! empty($items))
    <section class="border-t-4 border-slate-950 bg-slate-100">
        <div class="mx-auto max-w-3xl px-6 py-16">
            @if ($heading = $data['heading'] ?? null)
                <h2 class="mb-10 text-center text-3xl font-black uppercase tracking-tight text-slate-900 sm:text-4xl">{{ $heading }}</h2>
            @endif
            <div class="space-y-4" x-data="{ open: null }">
                @foreach ($items as $index => $item)
                    <div class="box-brutal">
                        <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                                class="flex w-full items-center justify-between p-5 text-left text-base font-black uppercase tracking-tight text-slate-900">
                            <span>{{ $item['question'] ?? '' }}</span>
                            <span x-text="open === {{ $index }} ? '−' : '+'" class="ml-4 text-xl"></span>
                        </button>
                        <div x-show="open === {{ $index }}" x-cloak class="border-t-2 border-slate-950 p-5 text-sm leading-relaxed text-slate-600">
                            {{ $item['answer'] ?? '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
