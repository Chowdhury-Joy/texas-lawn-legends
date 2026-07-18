<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Support\PageBlockData;
use App\Support\PageBlocks;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * GrapesJS-driven visual editor for a Page's blocks. GrapesJS only ever
 * reports ordering/add/remove of opaque, server-rendered blocks — it never
 * serializes block content itself. Content is always edited through the
 * Filament form in the side panel, keeping the canvas from drifting out of
 * sync with the real data.
 *
 * @property-read Schema $form
 */
class PageBuilder extends Component implements HasForms
{
    use InteractsWithForms;

    public Page $page;

    /**
     * Ordered list of ['uuid', 'type', 'data', 'html'].
     *
     * @var array<int, array<string, mixed>>
     */
    public array $blocks = [];

    public ?string $selectedUuid = null;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

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

    public function form(Schema $schema): Schema
    {
        $type = $this->selectedType();

        return $schema
            ->components($type ? PageBlocks::fields($type) : [])
            ->statePath('data');
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

    public function selectBlock(string $uuid): void
    {
        $block = collect($this->blocks)->firstWhere('uuid', $uuid);

        if (! $block) {
            return;
        }

        $this->selectedUuid = $uuid;
        $this->data = $block['data'];
        $this->form->fill($this->data);
    }

    public function cancelSelection(): void
    {
        $this->selectedUuid = null;
        $this->data = [];
    }

    public function saveBlock(): void
    {
        $index = collect($this->blocks)->search(fn (array $b): bool => $b['uuid'] === $this->selectedUuid);

        if ($index === false) {
            return;
        }

        $data = $this->form->getState();

        $this->blocks[$index]['data'] = $data;
        $this->blocks[$index]['html'] = $this->renderBlock($this->blocks[$index]['type'], $data);

        $this->dispatch('block-updated', uuid: $this->selectedUuid, html: $this->blocks[$index]['html']);

        $this->selectedUuid = null;
        $this->data = [];
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

        if ($this->selectedUuid === $uuid) {
            $this->cancelSelection();
        }
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
    }

    private function selectedType(): ?string
    {
        return collect($this->blocks)->firstWhere('uuid', $this->selectedUuid)['type'] ?? null;
    }

    public function render()
    {
        return view('livewire.admin.page-builder');
    }
}
