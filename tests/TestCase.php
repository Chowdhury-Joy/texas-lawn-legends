<?php

namespace Tests;

use App\Models\Setting;
use App\Support\PageBlockData;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // PageBlockData memoises per request; tests share a process, so clear
        // it or a later test sees an earlier test's rows.
        PageBlockData::flush();

        // Same for the Setting per-request memo.
        Setting::flushRequestCache();
    }
}
