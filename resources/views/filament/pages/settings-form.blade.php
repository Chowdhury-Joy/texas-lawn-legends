<x-filament-panels::page>
    {{--
        No max-width cap — admin content shell is already full-width
        (AdminPanelProvider maxContentWidth Full). Keep forms edge-to-edge
        with the shell so Estimator & Pricing and sibling settings pages
        do not sit in a narrow column with empty space on the right.

        No visible "unsaved changes" indicator here on purpose: an x-show
        badge bound to `dirty` gets left in a stale visual state across a
        Livewire morph (e.g. right after this same form's own save
        request), so it can misleadingly read "unsaved" immediately after
        a successful save. The beforeunload guard below doesn't have that
        problem — it only reads `dirty` at the moment of unload, never
        renders it — so it stays.
    --}}
    <form wire:submit="save" class="w-full space-y-6"
          x-data="{ dirty: false }"
          x-init="window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } })"
          @input.capture="dirty = true"
          @change.capture="dirty = true"
          @submit="dirty = false">
        {{ $this->form }}

        <div class="flex items-center justify-end" style="margin-top: 24px">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament-panels::page>
