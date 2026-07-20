<div
    x-data="{ isDark: false }"
    :class="{ 'dark bg-slate-950 text-white': isDark }"
    x-init="
        window.addEventListener('message', (e) => {
            // Only trust the editor on our own origin.
            if (e.origin !== window.location.origin) return;
            if (!e.data) return;
            if (e.data.type === 'page-preview:blocks') {
                $wire.set('blocks', e.data.blocks, () => $wire.$refresh());
            }
            if (e.data.type === 'page-preview:darkMode') {
                isDark = Boolean(e.data.isDark);
            }
        });
    ">

    @foreach ((array) $blocks as $block)
        @php
            $blockType = $block['type'] ?? null;
            $isValid = $blockType && in_array($blockType, \App\Support\PageBlocks::all(), true) && view()->exists('blocks.'.$blockType);
        @endphp
        @if ($isValid)
            <div data-reveal>
                @include('blocks.'.$blockType, ['data' => $block['data'] ?? []])
            </div>
        @endif
    @endforeach
</div>
