@php
    use Illuminate\Support\Facades\Vite;
@endphp

<div
    class="page-builder"
    x-data
    x-init="window.PageBuilder.init($wire, {
        canvas: $refs.canvas,
        blocksPanel: $refs.blocksPanel,
        layersPanel: $refs.layersPanel,
        cssUrl: @js(Vite::asset('resources/css/app.css')),
        blocks: @js($blocks),
        labels: @js($this->labels()),
        availableTypes: @js($this->availableTypes()),
    })"
>
    @vite(['resources/css/page-builder.css', 'resources/js/page-builder.js'])

    <div class="page-builder__toolbar">
        <span class="page-builder__title">{{ $page->title }}</span>

        <div class="page-builder__device-selector">
            <button type="button" class="page-builder__device-btn active" data-device="desktop" title="Desktop View">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </button>
            <button type="button" class="page-builder__device-btn" data-device="tablet" title="Tablet View">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </button>
            <button type="button" class="page-builder__device-btn" data-device="mobile" title="Mobile View">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </button>
        </div>

        <button type="button" id="page-builder-save" class="page-builder__save">Save Page</button>
    </div>

    <div class="page-builder__body">
        <div class="page-builder__sidebar">
            <div class="page-builder__panel-label">Add a section</div>
            <div wire:ignore x-ref="blocksPanel" class="page-builder__blocks"></div>

            <div class="page-builder__panel-label">Sections</div>
            <div wire:ignore x-ref="layersPanel" class="page-builder__layers"></div>
        </div>

        <div wire:ignore class="page-builder__canvas-wrap">
            <div x-ref="canvas"></div>
        </div>

        <div class="page-builder__side-panel">
            @if ($selectedUuid)
                <div class="page-builder__panel-label">Edit {{ $this->labels()[collect($blocks)->firstWhere('uuid', $selectedUuid)['type'] ?? ''] ?? 'block' }}</div>

                {{ $this->form }}

                <div class="page-builder__panel-actions">
                    <button type="button" wire:click="saveBlock" class="page-builder__save">Save block</button>
                    <button type="button" wire:click="cancelSelection" class="page-builder__cancel">Cancel</button>
                </div>
            @else
                <p class="page-builder__hint">Select a section on the canvas to edit its content.</p>
            @endif
        </div>
    </div>
</div>
