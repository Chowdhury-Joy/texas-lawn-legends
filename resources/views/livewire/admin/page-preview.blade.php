<div
    x-data="{}"
    x-init="
        window.addEventListener('message', (e) => {
            if (!e.data || e.data.type !== 'page-preview:blocks') return;
            $wire.set('blocks', e.data.blocks, () => $wire.$refresh());
        });
    ">

    @foreach ((array) $blocks as $block)
        <div data-reveal>
            @include('blocks.'.$block['type'], ['data' => $block['data'] ?? []])
        </div>
    @endforeach
</div>
