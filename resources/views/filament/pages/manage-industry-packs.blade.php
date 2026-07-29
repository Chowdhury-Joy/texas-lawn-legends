<x-filament-panels::page>
    <div class="max-w-3xl space-y-6">
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100">
            <p class="font-semibold">Sales demo tip</p>
            <p class="mt-1 opacity-90">
                Prefer the public hub at
                <a href="{{ url('/demo') }}" class="underline font-bold" target="_blank" rel="noopener">/demo</a>
                when pitching — big industry cards, one click, then walk hero → quote → admin. Use this page to Load or Restore model home between meetings.
            </p>
            <p class="mt-2 text-xs opacity-80">
                Active now: <strong>{{ \App\Support\Niche\NicheResolver::active()->label() }}</strong>
                @if (\App\Support\Niche\NicheResolver::demoMode())
                    · Demo banner on
                @endif
            </p>
        </div>

        {{ $this->form }}
    </div>
</x-filament-panels::page>
