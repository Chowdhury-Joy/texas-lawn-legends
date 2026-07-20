<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Support\PageBlocks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_and_path_traversal_block_types_are_safely_ignored(): void
    {
        $page = Page::create([
            'title' => 'Security Test Page',
            'slug' => 'security-test-page',
            'is_published' => true,
            'blocks' => [
                ['type' => '../../layouts/app', 'data' => []],
                ['type' => 'non_existent_block_type', 'data' => []],
                ['type' => 'cta_banner', 'data' => ['heading' => 'Valid CTA Banner']],
            ],
        ]);

        $response = $this->get('/security-test-page');

        $response->assertStatus(200);
        $response->assertSee('Valid CTA Banner');
    }

    public function test_tailwind_safelist_contains_tablet_and_desktop_utility_classes(): void
    {
        $safelist = PageBlocks::tailwindSafelist();

        $this->assertIsArray($safelist);
        $this->assertContains('tab:grid-cols-4', $safelist);
        $this->assertContains('tab:gap-6', $safelist);
        $this->assertContains('lg:grid-cols-12', $safelist);
        $this->assertContains('lg:justify-between', $safelist);
    }
}
