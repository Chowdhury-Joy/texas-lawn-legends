<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\PageBlockData;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.show', [
            'page' => Page::home(),
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
