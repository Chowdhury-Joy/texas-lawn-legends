<x-filament-panels::page>
    <div
        x-data="{
            theme: '{{ request('preview_theme', setting('theme', 'clean')) }}',
            device: 'desktop',
            base: '{{ route('admin.page-preview') }}',
            page: '{{ $this->getPageRecord()->slug ?? '' }}',
            encodedBlocks() {
                try { return encodeURIComponent(JSON.stringify($wire.data.blocks ?? [])); }
                catch (e) { return ''; }
            },
            url() {
                const params = new URLSearchParams({ preview_theme: this.theme });
                if (this.page) params.set('page', this.page);
                const b = this.encodedBlocks();
                if (b) params.set('blocks', b);
                return this.base + '?' + params.toString();
            },
            refresh() {
                const frame = this.$refs.frame;
                if (frame) frame.contentWindow.location.href = this.url();
            },
            push() {
                const frame = this.$refs.frame;
                if (frame && frame.contentWindow) {
                    frame.contentWindow.postMessage({ type: 'page-preview:blocks', blocks: $wire.data.blocks ?? [] }, '*');
                }
            }
        }"
        x-init="$watch('theme', () => refresh()); $watch('$wire.data.blocks', () => push()); setTimeout(() => push(), 500);"
        class="grid gap-6 lg:grid-cols-2">

        {{-- Left: editor --}}
        <div class="space-y-6">
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="flex justify-end gap-3">
                    @foreach ($this->getFormActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </form>
        </div>

        {{-- Right: live preview --}}
        <div class="lg:sticky lg:top-6 lg:self-start">
            <div class="mb-3 flex items-center justify-between gap-3 rounded-xl border border-gray-300 bg-white p-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Theme</label>
                    <select x-model="theme" class="rounded-lg border-gray-300 text-sm">
                        @foreach (array_keys(\App\Support\PageBlocks::themes()) as $t)
                            <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-1">
                    <template x-for="d in ['desktop', 'tablet', 'mobile']" :key="d">
                        <button type="button"
                                @click="device = d; $nextTick(() => refresh())"
                                :class="device === d ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="rounded-lg px-3 py-1.5 text-xs font-semibold capitalize"
                                x-text="d"></button>
                    </template>
                </div>
                <button type="button" @click="refresh()"
                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                    Refresh
                </button>
            </div>

            <div class="overflow-hidden rounded-xl border-4 border-slate-950 bg-white shadow-lg"
                 :class="{ 'max-w-[768px] mx-auto': device === 'tablet', 'max-w-[390px] mx-auto': device === 'mobile' }">
                <iframe
                    x-ref="frame"
                    :src="url()"
                    title="Page preview"
                    class="h-[75vh] w-full border-0 bg-white"></iframe>
            </div>
            <p class="mt-2 text-center text-xs text-gray-400">Preview updates live as you edit. Switch theme/device to reload.</p>
        </div>
    </div>
</x-filament-panels::page>
