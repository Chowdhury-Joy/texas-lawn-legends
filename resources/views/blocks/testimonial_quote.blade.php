@php
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
    $avatar = filled($data['avatar'] ?? null) ? public_url($data['avatar']) : null;
@endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="mx-auto max-w-7xl px-6 py-20">
        <figure class="{{ $layout ?: 'flex flex-col items-center gap-6 text-center' }}">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" class="inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif

            <blockquote data-field="quote" class="max-w-3xl text-2xl font-medium leading-snug tracking-tighter text-slate-900 sm:text-3xl">
                “{{ filled($data['quote'] ?? null) ? $data['quote'] : '[A standout client result worth featuring front and center.]' }}”
            </blockquote>

            <figcaption class="flex items-center gap-3">
                @if ($avatar)
                    <img src="{{ $avatar }}" alt="{{ $data['author'] ?? '' }}" class="h-12 w-12 rounded-full border-2 border-slate-950 object-cover">
                @endif
                <span class="text-left">
                    <span data-field="author" class="block text-sm font-bold uppercase tracking-wide text-slate-900">{{ filled($data['author'] ?? null) ? $data['author'] : '[Client Name]' }}</span>
                    @if (filled($data['role'] ?? null))
                        <span data-field="role" class="block text-xs font-medium text-slate-500">{{ $data['role'] }}</span>
                    @endif
                </span>
            </figcaption>
        </figure>
    </div>
</section>
