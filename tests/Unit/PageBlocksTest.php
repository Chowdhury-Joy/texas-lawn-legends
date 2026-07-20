<?php

namespace Tests\Unit;

use App\Support\PageBlocks;
use PHPUnit\Framework\TestCase;

class PageBlocksTest extends TestCase
{
    public function test_themes_returns_expected_theme_list(): void
    {
        $themes = PageBlocks::themes();

        $this->assertIsArray($themes);
        $this->assertArrayHasKey('clean', $themes);
        $this->assertArrayHasKey('minimal', $themes);
        $this->assertArrayHasKey('editorial', $themes);
        $this->assertArrayHasKey('rounded', $themes);
        $this->assertArrayHasKey('retro', $themes);
        $this->assertArrayHasKey('bold', $themes);
    }

    public function test_layout_classes_builds_correct_tailwind_string(): void
    {
        $layout = [
            'mobile' => [
                'display' => 'grid',
                'columns' => 1,
                'gap' => '4',
                'justify' => 'start',
                'items' => 'start',
            ],
            'tablet' => [
                'display' => 'grid',
                'columns' => 2,
                'gap' => '6',
                'justify' => 'center',
                'items' => 'center',
            ],
            'desktop' => [
                'display' => 'flex',
                'direction' => 'row',
                'wrap' => 'nowrap',
                'gap' => '8',
                'justify' => 'between',
                'items' => 'end',
            ],
        ];

        $classes = PageBlocks::layoutClasses($layout);

        // Mobile assertions (base)
        $this->assertStringContainsString('grid', $classes);
        $this->assertStringContainsString('grid-cols-1', $classes);
        $this->assertStringContainsString('gap-4', $classes);

        // Tablet assertions (tab: prefix)
        $this->assertStringContainsString('tab:grid', $classes);
        $this->assertStringContainsString('tab:grid-cols-2', $classes);
        $this->assertStringContainsString('tab:gap-6', $classes);

        // Desktop assertions (lg: prefix)
        $this->assertStringContainsString('lg:flex', $classes);
        $this->assertStringContainsString('lg:flex-row', $classes);
        $this->assertStringContainsString('lg:gap-8', $classes);
        $this->assertStringContainsString('lg:justify-between', $classes);
        $this->assertStringContainsString('lg:items-end', $classes);
    }

    public function test_layout_classes_returns_empty_string_when_layout_is_null_or_empty(): void
    {
        $this->assertSame('', PageBlocks::layoutClasses(null));
        $this->assertSame('', PageBlocks::layoutClasses([]));
    }
}
