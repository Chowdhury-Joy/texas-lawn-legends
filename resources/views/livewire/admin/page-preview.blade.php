<div
    x-data="{}"
    x-init="
        window.addEventListener('message', (e) => {
            if (!e.data || e.data.type !== 'page-preview:blocks') return;
            $wire.set('blocks', e.data.blocks, () => $wire.$refresh());
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
