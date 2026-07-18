@php
    use Illuminate\Support\Facades\Vite;
@endphp

<div
    class="page-builder"
    x-data="{ leftSidebarOpen: true, rightSidebarOpen: true, blocksOpen: false }"
    :class="{ 
        'left-sidebar-collapsed': !leftSidebarOpen, 
        'right-sidebar-collapsed': !rightSidebarOpen 
    }"
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

    <!-- Topbar -->
    <div class="page-builder__toolbar">
        <div class="page-builder__toolbar-left">
            <button type="button" @click="leftSidebarOpen = !leftSidebarOpen" class="page-builder__toggle-btn" title="Toggle Left Sidebar" aria-label="Toggle Left Sidebar">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <button type="button" @click="blocksOpen = !blocksOpen" class="page-builder__add-btn" :class="{ 'active': blocksOpen }" title="Add Section" aria-label="Add Section">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </button>
            <span class="page-builder__title">{{ $page->title }}</span>
        </div>

        <div class="page-builder__toolbar-center">
            <div class="page-builder__device-selector" role="group" aria-label="Device View Selector">
                <button type="button" class="page-builder__device-btn active" data-device="desktop" title="Desktop View" aria-label="Switch to Desktop View">
                    Desktop
                </button>
                <button type="button" class="page-builder__device-btn" data-device="tablet" title="Tablet View" aria-label="Switch to Tablet View">
                    Tablet
                </button>
                <button type="button" class="page-builder__device-btn" data-device="mobile" title="Mobile View" aria-label="Switch to Mobile View">
                    Mobile
                </button>
            </div>
        </div>

        <div class="page-builder__toolbar-right">
            <div class="page-builder__history-controls" role="group" aria-label="History Control Buttons">
                <button type="button" class="page-builder__toolbar-btn" id="pb-undo" title="Undo" aria-label="Undo">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                </button>
                <button type="button" class="page-builder__toolbar-btn" id="pb-redo" title="Redo" aria-label="Redo">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l6-6m0 0l-6-6m6 6H9a6 6 0 000 12h3"/></svg>
                </button>
            </div>
            <button type="button" class="page-builder__toolbar-btn" id="pb-preview" title="Toggle Preview Mode" aria-label="Toggle Preview Mode">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
            <button type="button" id="page-builder-save" class="page-builder__save" aria-label="Save Page Changes">Save Page</button>
            <button type="button" @click="rightSidebarOpen = !rightSidebarOpen" class="page-builder__toggle-btn" title="Toggle Right Sidebar" aria-label="Toggle Right Sidebar">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Workspace Body -->
    <div class="page-builder__body">
        <!-- Left Sidebar (Pages & Layers) -->
        <div class="page-builder__sidebar page-builder__sidebar--left" x-show="leftSidebarOpen" x-cloak x-transition>
            <!-- Pages Section -->
            <div class="page-builder__panel-section">
                <div class="page-builder__panel-label">Pages</div>
                <div class="page-builder__pages-list">
                    <div class="page-builder__page-item active">
                        <svg class="w-4 h-4 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        <span>{{ $page->title }}</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="page-builder__panel-divider"></div>

            <!-- Layers Section -->
            <div class="page-builder__panel-section flex-1 flex flex-col min-h-0">
                <div class="page-builder__panel-label">Layers</div>
                <div wire:ignore x-ref="layersPanel" class="page-builder__layers flex-1 overflow-y-auto"></div>
            </div>
        </div>

        <!-- Sliding Blocks panel (Add Section) -->
        <div class="page-builder__blocks-drawer" x-show="blocksOpen" @click.away="blocksOpen = false" x-cloak x-transition>
            <div class="page-builder__panel-label">Add a section</div>
            <div wire:ignore x-ref="blocksPanel" class="page-builder__blocks"></div>
        </div>

        <!-- Canvas Area -->
        <div wire:ignore class="page-builder__canvas-wrap">
            <div x-ref="canvas"></div>
        </div>

        <!-- Right Sidebar (Styles & Properties) -->
        <div class="page-builder__sidebar page-builder__sidebar--right" x-show="rightSidebarOpen" x-cloak x-transition x-data="{ activeRightTab: 'styles' }">
            <div class="page-builder__tabs" role="tablist" aria-label="Design Settings Tabs">
                <button type="button" class="page-builder__tab" role="tab" :aria-selected="activeRightTab === 'styles'" aria-controls="panel-styles" id="tab-btn-styles" :class="{ 'active': activeRightTab === 'styles' }" @click="activeRightTab = 'styles'">Styles</button>
                <button type="button" class="page-builder__tab" role="tab" :aria-selected="activeRightTab === 'traits'" aria-controls="panel-traits" id="tab-btn-traits" :class="{ 'active': activeRightTab === 'traits' }" @click="activeRightTab = 'traits'">Properties</button>
            </div>

            <div class="page-builder__sidebar-panel-body">
                <div class="page-builder__tab-content" id="panel-styles" role="tabpanel" aria-labelledby="tab-btn-styles" x-show="activeRightTab === 'styles'">
                    <div wire:ignore x-ref="stylesPanel" class="page-builder__styles"></div>
                </div>
                <div class="page-builder__tab-content" id="panel-traits" role="tabpanel" aria-labelledby="tab-btn-traits" x-show="activeRightTab === 'traits'" x-cloak>
                    <div wire:ignore x-ref="traitsPanel" class="page-builder__traits"></div>
                </div>
            </div>
        </div>
    </div>
</div>
