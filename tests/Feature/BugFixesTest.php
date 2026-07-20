<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Testimonial;
use App\Support\PageBlockData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_seo_image_uses_public_url_helper(): void
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
            'is_published' => true,
            'seo_image' => 'pages/share.jpg',
            'blocks' => [],
        ]);

        $response = $this->get('/test-page');
        $response->assertStatus(200);
        $response->assertSee('/storage/pages/share.jpg', false);
    }

    public function test_page_block_data_live_returns_all_testimonials(): void
    {
        Testimonial::factory()->create([
            'is_featured' => true,
            'author' => 'Featured Author',
            'neighborhood' => 'Kessler Park',
            'review_text' => 'Awesome service!',
            'rating' => 5,
        ]);
        Testimonial::factory()->create([
            'is_featured' => false,
            'author' => 'Non-Featured Author',
            'neighborhood' => 'Bishop Arts',
            'review_text' => 'Very reliable team.',
            'rating' => 5,
        ]);

        $data = PageBlockData::live();

        $this->assertCount(2, $data['testimonials']);
        $this->assertTrue($data['testimonials']->contains('author', 'Non-Featured Author'));
    }

    public function test_gallery_and_logo_cloud_do_not_contain_via_placeholder_urls(): void
    {
        $blocks = [
            [
                'type' => 'gallery',
                'data' => [
                    'images' => [
                        ['image' => null, 'caption' => 'Sample Image'],
                    ],
                ],
            ],
            [
                'type' => 'logo_cloud',
                'data' => [
                    'logos' => [
                        ['image' => null, 'label' => 'Sample Logo'],
                    ],
                ],
            ],
        ];

        $page = Page::create([
            'title' => 'Blocks Test',
            'slug' => 'blocks-test',
            'is_published' => true,
            'blocks' => $blocks,
        ]);

        $response = $this->get('/blocks-test');
        $response->assertStatus(200);
        $response->assertDontSee('via.placeholder.com');
        $response->assertSee('data:image/svg+xml;utf8');
    }
}
