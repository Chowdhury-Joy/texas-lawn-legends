<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Support\PageBlockData;
use App\Support\PageBlocks;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * GrapesJS-driven visual editor for a Page's blocks. GrapesJS reports
 * ordering, additions, and removals of server-rendered blocks.
 * Content is edited directly on the canvas using inline editing.
 */
class PageBuilder extends Component
{
    public Page $page;

    /**
     * Ordered list of ['uuid', 'type', 'data', 'html'].
     *
     * @var array<int, array<string, mixed>>
     */
    public array $blocks = [];

    public function mount(Page $page): void
    {
        $this->page = $page;

        $this->blocks = collect($page->blocks ?? [])
            ->map(fn (array $block, string $uuid): array => [
                'uuid' => $uuid,
                'type' => $block['type'],
                'data' => $block['data'] ?? [],
                'html' => $this->renderBlock($block['type'], $block['data'] ?? []),
            ])
            ->values()
            ->all();
    }

    public function labels(): array
    {
        return PageBlocks::labels();
    }

    /**
     * @return array<int, string>
     */
    public function availableTypes(): array
    {
        return $this->page->is_home ? PageBlocks::forHomepage() : PageBlocks::all();
    }

    /**
     * @return array{uuid: string, html: string}
     */
    public function addBlock(string $type): array
    {
        $uuid = (string) Str::uuid();
        $data = [];
        $html = $this->renderBlock($type, $data);

        $this->blocks[] = compact('uuid', 'type', 'data', 'html');

        return compact('uuid', 'html');
    }

    public function removeBlock(string $uuid): void
    {
        $this->blocks = collect($this->blocks)
            ->reject(fn (array $b): bool => $b['uuid'] === $uuid)
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $uuids
     */
    public function reorder(array $uuids): void
    {
        $byUuid = collect($this->blocks)->keyBy('uuid');

        $this->blocks = collect($uuids)
            ->map(fn (string $uuid) => $byUuid->get($uuid))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function renderBlock(string $type, array $data): string
    {
        return view('blocks.'.$type, ['data' => $data, ...PageBlockData::live()])->render();
    }

    public function save(): void
    {
        $stored = [];

        foreach ($this->blocks as $block) {
            $stored[$block['uuid']] = ['type' => $block['type'], 'data' => $block['data']];
        }

        $this->page->update(['blocks' => $stored]);

        Notification::make()
            ->title('Page saved')
            ->success()
            ->send();

        // Auto-refresh the browser page after updating
        $this->js('window.location.reload()');
    }

    public function savePageWithData(array $uuids, array $blockDataUpdates): void
    {
        // 1. Reorder blocks based on client order
        $this->reorder($uuids);

        // 2. Update block data arrays with client-edited field values
        foreach ($this->blocks as $index => $block) {
            $uuid = $block['uuid'];
            if (isset($blockDataUpdates[$uuid])) {
                foreach ($blockDataUpdates[$uuid] as $field => $value) {
                    // Clear out placeholder values (e.g. '[Eyebrow]' -> '')
                    if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                        $value = '';
                    }
                    
                    data_set($this->blocks[$index]['data'], $field, $value);
                }
                // Update rendered HTML to match
                $this->blocks[$index]['html'] = $this->renderBlock($block['type'], $this->blocks[$index]['data']);
            }
        }

        // 3. Save to database
        $this->save();
    }

    public function render()
    {
        return view('livewire.admin.page-builder');
    }
}
