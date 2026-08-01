<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\ColorContrast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandColorContrastTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_publishes_on_accent_ink_for_dark_accent(): void
    {
        Setting::set('color_accent', '#0f172a', 'string', 'branding');
        Setting::set('color_primary', '#1b4332', 'string', 'branding');
        Setting::set('color_primary_light', '#2d6a4f', 'string', 'branding');
        Setting::set('site_name', 'Demo Site', 'string', 'general');
        Setting::set('logo_text', 'Demo Site', 'string', 'branding');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('--color-on-accent: '.ColorContrast::NEAR_WHITE, $html);
        $this->assertStringContainsString('--color-yellow-400: #0f172a', $html);
        $this->assertStringContainsString('--color-on-primary: '.ColorContrast::NEAR_WHITE, $html);
    }

    public function test_layout_publishes_near_black_on_accent_for_light_accent(): void
    {
        Setting::set('color_accent', '#facc15', 'string', 'branding');
        Setting::set('site_name', 'Demo Site', 'string', 'general');
        Setting::set('logo_text', 'Demo Site', 'string', 'branding');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('--color-on-accent: '.ColorContrast::NEAR_BLACK, $html);
    }

    public function test_layout_falls_back_when_structural_slate_is_too_light(): void
    {
        Setting::set('color_slate', '#d6d6d6', 'string', 'branding');
        Setting::set('site_name', 'Demo Site', 'string', 'general');
        Setting::set('logo_text', 'Demo Site', 'string', 'branding');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('--color-slate-800: '.ColorContrast::DEFAULT_SLATE, $html);
        $this->assertStringNotContainsString('--color-slate-800: #d6d6d6', $html);
    }

    public function test_layout_keeps_dark_structural_slate(): void
    {
        Setting::set('color_slate', '#1e293b', 'string', 'branding');
        Setting::set('site_name', 'Demo Site', 'string', 'general');
        Setting::set('logo_text', 'Demo Site', 'string', 'branding');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('--color-slate-800: #1e293b', $html);
    }
}
