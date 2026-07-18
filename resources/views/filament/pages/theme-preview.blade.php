<div x-data="{ previewTheme: '{{ request('preview_theme', setting('theme', 'clean')) }}' }" class="space-y-3">
    <div class="flex flex-wrap items-center gap-3">
        <label for="preview-theme" class="text-sm font-semibold text-slate-700">Preview theme</label>
        <select id="preview-theme" x-model="previewTheme"
                class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900">
            <option value="clean">Clean</option>
            <option value="minimal">Minimal</option>
            <option value="editorial">Editorial</option>
            <option value="rounded">Rounded</option>
            <option value="retro">Retro</option>
            <option value="bold">Bold</option>
        </select>
        <span class="text-xs text-slate-500">Live preview — does not save. Click “Save changes” to apply a theme.</span>
    </div>

    <iframe
        x-bind:src="'/?preview_theme=' + previewTheme"
        title="Theme preview"
        class="h-[480px] w-full rounded-lg border border-slate-300 bg-white"
        loading="lazy"></iframe>
</div>
