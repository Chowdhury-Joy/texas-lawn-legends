@php
    use Illuminate\Support\Facades\Vite;
@endphp

<div
    class="page-builder"
    x-data="{ sidebarOpen: true, activeTab: 'blocks' }"
    :class="{ 'sidebar-collapsed': !sidebarOpen }"
    x-init="window.PageBuilder.init($wire, {
        canvas: $refs.canvas,
        blocksPanel: $refs.blocksPanel,
        layersPanel: $refs.layersPanel,
        stylesPanel: $refs.stylesPanel,
        traitsPanel: $refs.traitsPanel,
        cssUrl: @js(Vite::asset('resources/css/app.css')),
        blocks: @js($blocks),
        labels: @js($this->labels()),
        availableTypes: @js($this->availableTypes()),
    })"
>
    @vite(['resources/css/page-builder.css', 'resources/js/page-builder.js'])

    <div class="page-builder__toolbar">
        <div class="page-builder__title-wrap">
            <button type="button" @click="sidebarOpen = !sidebarOpen" class="page-builder__toggle-btn" title="Toggle Sidebar" aria-label="Toggle Sidebar Menu">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="page-builder__title">{{ $page->title }}</span>
        </div>

        <div class="page-builder__center-toolbar">
            <div class="page-builder__device-selector" role="group" aria-label="View Mode Device Selector">
                <button type="button" class="page-builder__device-btn active" data-device="desktop" title="Desktop View" aria-label="Switch to Desktop View">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </button>
                <button type="button" class="page-builder__device-btn" data-device="tablet" title="Tablet View" aria-label="Switch to Tablet View">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </button>
                <button type="button" class="page-builder__device-btn" data-device="mobile" title="Mobile View" aria-label="Switch to Mobile View">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </button>
            </div>

            <div class="page-builder__history-controls" role="group" aria-label="History Controls">
                <button type="button" class="page-builder__toolbar-btn" id="pb-undo" title="Undo (Cmd+Z)" aria-label="Undo last change">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                </button>
                <button type="button" class="page-builder__toolbar-btn" id="pb-redo" title="Redo (Cmd+Shift+Z)" aria-label="Redo undone change">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l6-6m0 0l-6-6m6 6H9a6 6 0 000 12h3"/></svg>
                </button>
            </div>
        </div>

        <div class="page-builder__actions">
            <button type="button" class="page-builder__toolbar-btn" id="pb-preview" title="Toggle Preview Mode" aria-label="Toggle Preview Mode">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
            <button type="button" id="page-builder-save" class="page-builder__save" aria-label="Save Page Changes">Save Page</button>
        </div>
    </div>

    <div class="page-builder__body">
        <div class="page-builder__sidebar">
            <div class="page-builder__sidebar-tabs" role="tablist" aria-label="Editor Sidebar Tabs">
                <button type="button" class="page-builder__sidebar-tab" role="tab" :aria-selected="activeTab === 'blocks'" aria-controls="panel-blocks" id="tab-btn-blocks" :class="{ 'active': activeTab === 'blocks' }" @click="activeTab = 'blocks'" title="Add Blocks">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                </button>
                <button type="button" class="page-builder__sidebar-tab" role="tab" :aria-selected="activeTab === 'layers'" aria-controls="panel-layers" id="tab-btn-layers" :class="{ 'active': activeTab === 'layers' }" @click="activeTab = 'layers'" title="Structure / Layers">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L12 7.5l5.571 2.25m-11.142 4.5L12 16.5l5.571-2.25m-11.142 0L2.25 12l4.179-2.25m11.142 4.5l4.179-2.25-4.179-2.25M6 16.5v2.25L12 21l6-2.25V16.5"/></svg>
                </button>
                <button type="button" class="page-builder__sidebar-tab" role="tab" :aria-selected="activeTab === 'styles'" aria-controls="panel-styles" id="tab-btn-styles" :class="{ 'active': activeTab === 'styles' }" @click="activeTab = 'styles'" title="Design / Styles">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-2.22 1.124l-3.147 4.294a.375.375 0 00.315.614h18.27a.375.375 0 00.315-.614l-3.15-4.29a3 3 0 00-2.217-1.128h-8.172zM10.5 7.5a2 2 0 11-4 0 2 2 0 014 0zm9 0a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </button>
                <button type="button" class="page-builder__sidebar-tab" role="tab" :aria-selected="activeTab === 'traits'" aria-controls="panel-traits" id="tab-btn-traits" :class="{ 'active': activeTab === 'traits' }" @click="activeTab = 'traits'" title="Element Settings">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                </button>
            </div>

            <div class="page-builder__sidebar-panel-body">
                <div class="page-builder__tab-content" id="panel-blocks" role="tabpanel" aria-labelledby="tab-btn-blocks" x-show="activeTab === 'blocks'">
                    <div class="page-builder__panel-label">Add a section</div>
                    <div wire:ignore x-ref="blocksPanel" class="page-builder__blocks"></div>
                </div>

                <div class="page-builder__tab-content" id="panel-layers" role="tabpanel" aria-labelledby="tab-btn-layers" x-show="activeTab === 'layers'" x-cloak>
                    <div class="page-builder__panel-label">Sections &amp; Items</div>
                    <div wire:ignore x-ref="layersPanel" class="page-builder__layers"></div>
                </div>

                <div class="page-builder__tab-content" id="panel-styles" role="tabpanel" aria-labelledby="tab-btn-styles" x-show="activeTab === 'styles'" x-cloak>
                    <div class="page-builder__panel-label">Styles Inspector</div>
                    <div wire:ignore x-ref="stylesPanel" class="page-builder__styles"></div>
                </div>

                <div class="page-builder__tab-content" id="panel-traits" role="tabpanel" aria-labelledby="tab-btn-traits" x-show="activeTab === 'traits'" x-cloak>
                    <div class="page-builder__panel-label">Properties Settings</div>
                    <div wire:ignore x-ref="traitsPanel" class="page-builder__traits"></div>
                </div>
            </div>
        </div>

        <div class="page-builder__resize-handle" id="sidebar-resize-handle"></div>

        <div wire:ignore class="page-builder__canvas-wrap">
            <div x-ref="canvas"></div>
        </div>
    </div>
</div>
