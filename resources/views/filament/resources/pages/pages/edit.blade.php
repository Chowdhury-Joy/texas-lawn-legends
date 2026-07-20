<x-filament-panels::page>
    <div class="max-w-5xl">
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            {{-- Floating Action Bar --}}
            <div class="sticky bottom-6 z-30 flex items-center justify-between gap-4 rounded-xl border border-gray-300 bg-white/95 px-5 py-3.5 shadow-xl backdrop-blur-md dark:border-gray-700 dark:bg-gray-900/95">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
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
