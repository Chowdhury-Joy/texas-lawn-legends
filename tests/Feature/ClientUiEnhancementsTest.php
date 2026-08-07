<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Project;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientUiEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_neighborhood_proof_renders_interactive_before_after_slider(): void
    {
        Testimonial::factory()->create([
            'is_featured' => true,
            'author' => 'Jane Smith',
            'neighborhood' => 'Kessler Park',
            'review_text' => 'Stunning work!',
            'rating' => 5,
        ]);

        $page = Page::create([
            'title' => 'Proof Test',
            'slug' => 'proof-test',
            'is_published' => true,
            'blocks' => [
                [
                    'type' => 'neighborhood_proof',
                    'data' => [
                        'heading' => 'Interactive Proof Slider',
                    ],
                ],
            ],
        ]);

        $response = $this->get('/proof-test');

        $response->assertStatus(200);
        $response->assertSee('x-data="{ pos: 50, dragging: false }"', false);
        $response->assertSee('After Build');
        $response->assertSee('Before Build');
    }

    public function test_client_dashboard_renders_photo_lightbox_modal(): void
    {
        $project = Project::factory()->create([
            'client_name' => 'John Doe',
            'project_title' => 'Flagstone Patio Build',
            'neighborhood' => 'Highland Park',
            'contract_value' => 5000.00,
            'started_at' => now(),
        ]);

        $response = $this->get('/dashboard/'.$project->unique_dashboard_hash);

        $response->assertStatus(200);
        $response->assertSee('showPhoto(', false);
        $response->assertSee('x-show="open"', false);
    }
}
