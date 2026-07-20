<x-filament-panels::page>
    <div class="max-w-5xl pb-16">
        {{--
            No visible "unsaved changes" indicator here on purpose: an x-show
            badge bound to `dirty` gets left in a stale visual state across a
            Livewire morph (e.g. right after this same form's own save
            request), so it can misleadingly read "unsaved" immediately after
            a successful save. The beforeunload guard below doesn't have that
            problem — it only reads `dirty` at the moment of unload, never
            renders it — so it stays.
        --}}
        <form wire:submit="save" class="space-y-6"
              x-data="{ dirty: false }"
              x-init="window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } })"
              @input.capture="dirty = true"
              @change.capture="dirty = true"
              @submit="dirty = false">
            {{ $this->form }}

            {{-- Sticky Floating Save Bar with solid background --}}
            <div class="sticky bottom-0 z-40 -mx-4 -mb-4 sm:-mx-6 sm:-mb-6 mt-8 flex items-center justify-between gap-4 border-t border-slate-200 bg-white px-6 py-4 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Page Content Editor</span>
                </div>
                <div class="flex items-center gap-3">
                    @foreach ($this->getFormActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>
