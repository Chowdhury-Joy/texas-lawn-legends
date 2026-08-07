<?php

namespace Tests\Feature;

use App\Models\Page;
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
}
