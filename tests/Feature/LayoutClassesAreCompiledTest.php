<?php

namespace Tests\Feature;

use App\Support\PageBlocks;
use Tests\TestCase;

/**
 * PageBlocks::layoutClasses() assembles class names at runtime, so they never
 * appear as literals in a file Tailwind scans. They only reach the stylesheet
 * via the `@source inline(...)` safelist in resources/css/app.css.
 *
 * This walks every class the editor can actually produce and asserts each one
 * survived into the built CSS — so adding a new Select option in
 * PageBlocks::layout() without extending the safelist fails here instead of
 * silently rendering as a no-op in production.
 */
class LayoutClassesAreCompiledTest extends TestCase
{
    /**
     * Every class layoutClasses() can emit, derived by driving the real
     * method rather than restating its output.
     *
     * @return array<int, string>
     */
    private function emittableClasses(): array
    {
        $columns = ['auto', '1', '2', '3', '4', '5', '6'];
        $gaps = ['2', '4', '6', '8', '10', '12'];
        $justify = ['start', 'center', 'end', 'between'];
        $items = ['start', 'center', 'end'];

        $rows = [];

        foreach ($gaps as $gap) {
            foreach ($justify as $j) {
                foreach ($items as $i) {
                    foreach ($columns as $col) {
                        $rows[] = ['display' => 'grid', 'columns' => $col, 'gap' => $gap, 'justify' => $j, 'items' => $i];
                    }

                    foreach (['row', 'col'] as $dir) {
                        foreach (['wrap', 'nowrap'] as $wrap) {
                            $rows[] = ['display' => 'flex', 'direction' => $dir, 'wrap' => $wrap, 'gap' => $gap, 'justify' => $j, 'items' => $i];
                        }
                    }
                }
            }
        }

        $classes = [];

        foreach (['mobile', 'tablet', 'desktop'] as $breakpoint) {
            foreach ($rows as $row) {
                foreach (explode(' ', PageBlocks::layoutClasses([$breakpoint => $row])) as $class) {
                    if ($class !== '') {
                        $classes[$class] = true;
                    }
                }
            }
        }

        return array_keys($classes);
    }

    private function builtCss(): ?string
    {
        $manifest = public_path('build/manifest.json');

        if (! file_exists($manifest)) {
            return null;
        }

        $entry = json_decode(file_get_contents($manifest), true)['resources/css/app.css']['file'] ?? null;

        if (! $entry || ! file_exists(public_path('build/'.$entry))) {
            return null;
        }

        return file_get_contents(public_path('build/'.$entry));
    }

    public function test_every_editor_selectable_layout_class_is_present_in_the_built_css(): void
    {
        $css = $this->builtCss();

        if ($css === null) {
            $this->markTestSkipped('No compiled CSS found — run `npm run build` first.');
        }

        $missing = [];

        foreach ($this->emittableClasses() as $class) {
            // Tailwind backslash-escapes every non-alphanumeric character in a
            // class selector, e.g. `.lg\:grid-cols-4` or, for an arbitrary
            // value, `.grid-cols-\[repeat\(auto-fit\,minmax\(200px\,1fr\)\)\]`.
            $selector = preg_replace('/([^a-zA-Z0-9_-])/', '\\\\$1', $class);

            if (! str_contains($css, '.'.$selector)) {
                $missing[] = $class;
            }
        }

        $this->assertSame(
            [],
            $missing,
            'These classes are reachable from the page-builder UI but absent from the built CSS. '
            .'Extend the @source inline(...) safelist in resources/css/app.css: '.implode(', ', $missing)
        );
    }
}
