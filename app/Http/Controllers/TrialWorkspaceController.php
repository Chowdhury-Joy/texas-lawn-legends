<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\PageBlockData;

class TrialWorkspaceController extends Controller
{
    public function home()
    {
        $page = Page::query()->where('is_home', true)->first();

        if ($page) {
            abort_unless($page->is_published, 404);
        } else {
            $page = new Page(['title' => 'Home', 'blocks' => [], 'is_published' => true]);
        }

        return view('pages.show', [
            'page' => $page,
            ...PageBlockData::live(),
        ]);
    }

    public function show(Page $page)
    {
        abort_unless($page->is_published, 404);

        return view('pages.show', [
            'page' => $page,
            ...PageBlockData::live(),
        ]);
    }
}
