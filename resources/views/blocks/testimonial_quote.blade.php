@php
    $layout = \App\Support\PageBlocks::layoutClasses($data['layout'] ?? []);
    $avatar = filled($data['avatar'] ?? null) ? public_url($data['avatar']) : null;
@endphp
<section class="border-t-4 border-slate-950 bg-brand-paper">
    <div class="layout-container space-section">
        <figure class="{{ $layout ?: 'flex flex-col items-center gap-container-lg text-center' }}">
            @if (filled($data['eyebrow'] ?? null))
                <span data-field="eyebrow" data-reveal class="inline-block w-fit bg-emerald-900 px-2 py-0.5 type-tagline text-yellow-400">
                    {{ $data['eyebrow'] }}
                </span>
            @endif

            @if (filled($data['quote'] ?? null))
                <blockquote data-field="quote" data-reveal class="type-h3 max-w-3xl text-slate-900">
                    “{{ $data['quote'] }}”
                </blockquote>
            @endif

            @if (filled($data['author'] ?? null) || $avatar)
                <figcaption data-reveal class="flex items-center gap-container-sm">
                    @if ($avatar)
                        <img src="{{ $avatar }}" alt="{{ $data['author'] ?? '' }}" class="h-12 w-12 rounded-full border-2 border-slate-950 object-cover">
                    @endif
                    @if (filled($data['author'] ?? null))
                        <span class="text-left">
                            <span data-field="author" class="type-tagline block text-slate-900">{{ $data['author'] }}</span>
                            @if (filled($data['role'] ?? null))
                                <span data-field="role" class="type-body-sm block text-slate-500">{{ $data['role'] }}</span>
                            @endif
                        </span>
                    @endif
                </figcaption>
            @endif
        </figure>
    </div>
</section>
