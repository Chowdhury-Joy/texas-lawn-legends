<?php

namespace Tests\Unit;

use App\Support\ColorContrast;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ColorContrastTest extends TestCase
{
    public function test_light_accent_gets_near_black_ink(): void
    {
        $this->assertSame(ColorContrast::NEAR_BLACK, ColorContrast::inkOn('#facc15'));
    }

    public function test_dark_accent_gets_near_white_ink(): void
    {
        $this->assertSame(ColorContrast::NEAR_WHITE, ColorContrast::inkOn('#0f172a'));
    }

    public function test_dark_primary_surfaces_get_near_white_ink(): void
    {
        $this->assertSame(ColorContrast::NEAR_WHITE, ColorContrast::inkOn('#1b4332'));
        $this->assertSame(ColorContrast::NEAR_WHITE, ColorContrast::inkOn('#2d6a4f'));
    }

    public function test_readable_accent_on_dark_card_keeps_accent_ink(): void
    {
        $this->assertSame('#facc15', ColorContrast::accentInk('#facc15', '#2d6a4f'));
    }

    public function test_body_ink_rejects_light_slate_on_white(): void
    {
        $this->assertSame(ColorContrast::DEFAULT_SLATE, ColorContrast::bodyInk('#d6d6d6'));
        $this->assertSame(ColorContrast::DEFAULT_SLATE, body_ink('#eee'));
    }

    public function test_body_ink_keeps_dark_slate(): void
    {
        $this->assertSame('#334155', ColorContrast::bodyInk('#334155'));
        $this->assertSame('#1e293b', body_ink('#1e293b'));
    }

    public function test_unreadable_accent_on_card_falls_back_to_surface_ink(): void
    {
        $this->assertSame(
            ColorContrast::NEAR_WHITE,
            ColorContrast::accentInk('#1b4332', '#2d6a4f'),
        );
    }

    public function test_helpers_delegate_to_contrast_class(): void
    {
        $this->assertSame(ColorContrast::NEAR_BLACK, contrast_ink('#facc15'));
        $this->assertSame('#facc15', accent_ink('#facc15', '#2d6a4f'));
    }

    #[DataProvider('hexNormalizationProvider')]
    public function test_short_hex_is_normalized(string $input, string $expectedInk): void
    {
        $this->assertSame($expectedInk, ColorContrast::inkOn($input));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function hexNormalizationProvider(): array
    {
        return [
            'short white' => ['#fff', ColorContrast::NEAR_BLACK],
            'short black' => ['#000', ColorContrast::NEAR_WHITE],
        ];
    }
}
