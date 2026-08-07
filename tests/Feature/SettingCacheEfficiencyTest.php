<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingCacheEfficiencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, array<string, mixed>>
     */
    private function settingQueries(string $key): array
    {
        return collect(DB::getQueryLog())
            ->filter(fn ($query) => str_contains($query['query'], 'settings')
                && str_contains(json_encode($query['bindings']), $key))
            ->values()
            ->all();
    }

    public function test_missing_setting_key_is_queried_once_per_request(): void
    {
        Cache::flush();
        Setting::flushRequestCache();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->assertSame('fallback', Setting::get('absent_key_probe', 'fallback'));
        $this->assertSame('fallback', Setting::get('absent_key_probe', 'fallback'));
        $this->assertSame('fallback', Setting::get('absent_key_probe', 'fallback'));

        $this->assertCount(1, $this->settingQueries('absent_key_probe'));
    }

    public function test_missing_setting_key_is_not_requeried_after_request_cache_flush(): void
    {
        Cache::flush();
        Setting::flushRequestCache();

        Setting::get('absent_key_probe', 'fallback');

        DB::flushQueryLog();
        DB::enableQueryLog();

        // Simulates a following request: the persistent cache must remember the miss.
        Setting::flushRequestCache();
        $this->assertSame('fallback', Setting::get('absent_key_probe', 'fallback'));

        $this->assertCount(0, $this->settingQueries('absent_key_probe'));
    }

    public function test_present_setting_is_resolved_once_per_request(): void
    {
        Setting::set('probe_present', 'hello', 'string', 'general');

        Cache::flush();
        Setting::flushRequestCache();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->assertSame('hello', Setting::get('probe_present'));
        $this->assertSame('hello', Setting::get('probe_present'));

        $this->assertCount(1, $this->settingQueries('probe_present'));
    }

    public function test_set_busts_both_cache_layers(): void
    {
        Setting::set('probe_mutable', 'before', 'string', 'general');
        $this->assertSame('before', Setting::get('probe_mutable'));

        Setting::set('probe_mutable', 'after', 'string', 'general');

        $this->assertSame('after', Setting::get('probe_mutable'));
    }

    public function test_deleting_a_setting_falls_back_to_default(): void
    {
        Setting::set('probe_deletable', 'value', 'string', 'general');
        $this->assertSame('value', Setting::get('probe_deletable'));

        Setting::query()->where('key', 'probe_deletable')->first()->delete();

        $this->assertSame('gone', Setting::get('probe_deletable', 'gone'));
    }

    public function test_legacy_cache_payload_shape_is_upgraded(): void
    {
        Setting::set('probe_legacy', 'legacy-value', 'string', 'general');
        Setting::flushRequestCache();

        // Emulate a payload written before the hit/miss shape shipped.
        Cache::forever('setting.probe_legacy', ['value' => 'legacy-value']);

        $this->assertSame('legacy-value', Setting::get('probe_legacy'));
    }

    public function test_null_valued_setting_is_distinguishable_from_a_miss(): void
    {
        Setting::set('probe_empty', '', 'string', 'general');

        Cache::flush();
        Setting::flushRequestCache();

        // Row exists with an empty value, so the default must not win.
        $this->assertSame('', Setting::get('probe_empty', 'default-should-not-win'));
    }
}
