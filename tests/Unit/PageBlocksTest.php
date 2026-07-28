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
        $this->assertArrayNotHasKey('editorial', $themes);
        $this->assertArrayHasKey('rounded', $themes);
        $this->assertArrayHasKey('retro', $themes);
        $this->assertArrayHasKey('bold', $themes);
    }

    public function test_resolve_theme_falls_back_from_removed_editorial(): void
    {
        $this->assertSame('clean', PageBlocks::resolveTheme('editorial'));
        $this->assertSame('clean', PageBlocks::resolveTheme(null));
        $this->assertSame('bold', PageBlocks::resolveTheme('bold'));
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

        // Desktop assertions (desk: prefix — 1200px device tier)
        $this->assertStringContainsString('desk:flex', $classes);
        $this->assertStringContainsString('desk:flex-row', $classes);
        $this->assertStringContainsString('desk:gap-8', $classes);
        $this->assertStringContainsString('desk:justify-between', $classes);
        $this->assertStringContainsString('desk:items-end', $classes);
    }

    public function test_layout_classes_returns_empty_string_when_layout_is_null_or_empty(): void
    {
        $this->assertSame('', PageBlocks::layoutClasses(null));
        $this->assertSame('', PageBlocks::layoutClasses([]));
    }

    /**
     * "auto" (or an absent `columns` key, since it's the default) sizes grid
     * columns to content instead of a fixed count, so a gallery of any item
     * count lays out cleanly without an editor ever picking a number.
     */
    public function test_layout_classes_uses_auto_fit_grid_when_columns_is_auto_or_absent(): void
    {
        $explicitAuto = PageBlocks::layoutClasses([
            'mobile' => ['display' => 'grid', 'columns' => 'auto', 'gap' => '6'],
        ]);
        $this->assertStringContainsString('grid-cols-[repeat(auto-fit,minmax(200px,1fr))]', $explicitAuto);
        $this->assertStringNotContainsString('grid-cols-3', $explicitAuto);

        $absentColumns = PageBlocks::layoutClasses([
            'desktop' => ['display' => 'grid', 'gap' => '6'],
        ]);
        $this->assertStringContainsString('desk:grid-cols-[repeat(auto-fit,minmax(200px,1fr))]', $absentColumns);
    }

    public function test_layout_classes_still_supports_an_explicit_fixed_column_count(): void
    {
        $classes = PageBlocks::layoutClasses([
            'mobile' => ['display' => 'grid', 'columns' => '4', 'gap' => '6'],
        ]);

        $this->assertStringContainsString('grid-cols-4', $classes);
        $this->assertStringNotContainsString('auto-fit', $classes);
    }
}
