<x-filament-panels::page>
    <div class="max-w-5xl pb-16">
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            {{-- Sticky Floating Save Bar with solid background --}}
            <div class="sticky bottom-0 z-40 -mx-4 -mb-4 sm:-mx-6 sm:-mb-6 mt-8 flex items-center justify-between gap-4 border-t border-slate-200 bg-white px-6 py-4 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Homepage Content Editor</span>
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
