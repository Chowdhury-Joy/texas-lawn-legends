<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\ProposalStatus;
use App\Models\Lead;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_proposal_can_be_created_and_viewed_publicly()
    {
        $lead = Lead::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'status' => LeadStatus::Qualified,
        ]);

        $proposal = Proposal::create([
            'lead_id' => $lead->id,
            'status' => ProposalStatus::Sent,
            'total_amount' => 5000,
            'content' => [
                ['type' => 'text_block', 'data' => ['content' => 'Hello John, here is your proposal.']],
            ],
        ]);

        $this->assertNotNull($proposal->unique_token);

        $response = $this->get(route('proposals.show', ['token' => $proposal->unique_token]));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('5,000.00');
        $response->assertSee('Hello John');
    }
}
