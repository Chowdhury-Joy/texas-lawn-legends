<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Support\PageBlockData;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

/**
 * Full-page live preview of page blocks, rendered through the public
 * site layout so it matches the real frontend (header, footer, themes).
 * The theme is controlled via the ?preview_theme= query param (already
 * honored by layouts.app.blade.php). Blocks come from an optional
 * ?blocks= JSON payload (live push) and otherwise fall back to the
 * saved page record (?page= slug, defaulting to the homepage).
 */
class PagePreview extends Component
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $blocks = [];

    public function mount(): void
    {
        $payload = Request::query('blocks');

        if (is_string($payload) && $decoded = json_decode($payload, true)) {
            $this->blocks = $decoded;

            return;
        }

        $slug = Request::query('page');
        $page = $slug ? Page::where('slug', $slug)->first() : Page::home();
        $this->blocks = $page?->blocks ?? [];
    }

    public function render()
    {
        return view('livewire.admin.page-preview', PageBlockData::live())
            ->layout('layouts.app');
    }
}
