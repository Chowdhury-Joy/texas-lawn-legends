<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Custom Filament Blade pages (Crew Schedule, Industry Packs tip, etc.) use
 * Tailwind utilities that are NOT in Filament's default stylesheet. Those
 * classes only exist if the panel Vite theme scans the custom views.
 *
 * Without resources/css/filament/admin/theme.css wired via viteTheme(), the
 * schedule page renders as bare white text on a black background.
 */
class FilamentThemeCompilesCustomAdminClassesTest extends TestCase
{
    /**
     * Classes that appear as literals in manage-schedule.blade.php and must
     * survive into the compiled Filament theme CSS.
     *
     * @return list<string>
     */
    private function requiredScheduleClasses(): array
    {
        return [
            'rounded-xl',
            'border-amber-400',
            'bg-amber-50',
            'dark:bg-amber-950/30',
            'bg-primary-600',
            'dark:bg-gray-900',
            'grid-cols-1',
            'lg:grid-cols-3',
        ];
    }

    private function filamentThemeCss(): ?string
    {
        $manifest = public_path('build/manifest.json');

        if (! file_exists($manifest)) {
            return null;
        }

        $entry = json_decode(file_get_contents($manifest), true)['resources/css/filament/admin/theme.css']['file'] ?? null;

        if (! $entry || ! file_exists(public_path('build/'.$entry))) {
            return null;
        }

        return file_get_contents(public_path('build/'.$entry));
    }

    public function test_crew_schedule_tailwind_classes_are_present_in_filament_theme(): void
    {
        $css = $this->filamentThemeCss();

        if ($css === null) {
            $this->markTestSkipped('No compiled Filament theme found — run `npm run build` first.');
        }

        $missing = [];

        foreach ($this->requiredScheduleClasses() as $class) {
            $selector = preg_replace('/([^a-zA-Z0-9_-])/', '\\\\$1', $class);

            if (! str_contains($css, '.'.$selector)) {
                $missing[] = $class;
            }
        }

        $this->assertSame(
            [],
            $missing,
            'Crew Schedule utilities missing from the Filament theme. Ensure '
            .'resources/css/filament/admin/theme.css @source includes resources/views/filament '
            .'and AdminPanelProvider registers ->viteTheme(): '.implode(', ', $missing)
        );
    }
}
