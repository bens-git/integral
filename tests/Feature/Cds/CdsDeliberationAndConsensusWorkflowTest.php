<?php

namespace Tests\Feature\Cds;

use App\Models\User;
use App\Models\Cds\Participant;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\DeliberationThread;
use App\Models\Cds\DeliberationMessage;
use App\Models\Cds\ConsensusModel;
use App\Models\Cds\Objection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CdsDeliberationAndConsensusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_deliberation_threads_can_be_listed(): void
    {
        $user = $this->actingAsUser();
        $issue = DecisionIssue::factory()->create();
        DeliberationThread::factory()->create(['issue_id' => $issue->id]);

        $response = $this->get("/cds/issues/{$issue->id}/deliberation");
        $response->assertStatus(200);
    }

    public function test_deliberation_thread_can_be_created(): void
    {
        $user = $this->actingAsUser();
        $issue = DecisionIssue::factory()->create();

        $response = $this->post("/cds/issues/{$issue->id}/deliberation", [
            'title' => 'Resource Allocation Discussion',
            'topic' => 'How to distribute funds fairly',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('deliberation_threads', ['title' => 'Resource Allocation Discussion']);
    }

    public function test_deliberation_message_can_be_sent(): void
    {
        $user = $this->actingAsUser();
        $thread = DeliberationThread::factory()->create();

        $response = $this->post("/deliberation/{$thread->id}/messages", [
            'message' => 'I support this proposal.',
            'stance' => 'support',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('deliberation_messages', [
            'thread_id' => $thread->id,
            'message' => 'I support this proposal.',
            'stance' => 'support',
        ]);
    }

    public function test_deliberation_thread_can_be_closed(): void
    {
        $user = $this->actingAsUser();
        $thread = DeliberationThread::factory()->open()->create();

        $response = $this->post("/threads/{$thread->id}/close");

        $response->assertRedirect();
        $this->assertDatabaseHas('deliberation_threads', ['id' => $thread->id, 'status' => 'closed']);
    }

    public function test_deliberation_thread_can_be_pinned(): void
    {
        $user = $this->actingAsUser();
        $thread = DeliberationThread::factory()->create(['is_pinned' => false]);

        $response = $this->post("/threads/{$thread->id}/pin");

        $response->assertRedirect();
        $this->assertDatabaseHas('deliberation_threads', ['id' => $thread->id, 'is_pinned' => true]);
    }

    public function test_deliberation_thread_can_be_locked(): void
    {
        $user = $this->actingAsUser();
        $thread = DeliberationThread::factory()->create(['is_locked' => false]);

        $response = $this->post("/threads/{$thread->id}/lock");

        $response->assertRedirect();
        $this->assertDatabaseHas('deliberation_threads', ['id' => $thread->id, 'is_locked' => true]);
    }

    public function test_consensus_can_be_created(): void
    {
        $user = $this->actingAsUser();
        $issue = DecisionIssue::factory()->create();

        $response = $this->post("/cds/issues/{$issue->id}/consensus", [
            'method' => 'weighted_consensus',
            'threshold' => 75,
            'summary' => 'Consensus on resource allocation',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('consensus_models', ['method' => 'weighted_consensus']);
        $this->assertDatabaseHas('decision_issues', ['id' => $issue->id, 'status' => 'consensus']);
    }

    public function test_vote_can_be_cast(): void
    {
        $user = $this->actingAsUser();
        $consensus = ConsensusModel::factory()->create(['total_votes' => 0]);

        $response = $this->post("/consensus/{$consensus->id}/vote", [
            'vote_type' => 'support',
            'rationale' => 'This is a good approach.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('consensus_models', [
            'id' => $consensus->id,
            'total_votes' => 1,
            'votes_support' => 1,
        ]);
    }

    public function test_objection_can_be_raised(): void
    {
        $user = $this->actingAsUser();
        $consensus = ConsensusModel::factory()->create();

        $response = $this->post("/consensus/{$consensus->id}/object", [
            'reason' => 'This approach has significant risks.',
            'objection_strength' => 4.5,
            'objection_type' => 'principled',
            'proposed_resolution' => 'Consider alternative approach.',
            'is_blocking' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('objection_map', [
            'consensus_id' => $consensus->id,
            'reason' => 'This approach has significant risks.',
            'is_blocking' => true,
        ]);
    }

    public function test_objection_can_be_resolved(): void
    {
        $user = $this->actingAsUser();
        $objection = Objection::factory()->blocking()->create();

        $response = $this->post("/objections/{$objection->id}/resolve", [
            'status' => 'resolved',
            'resolution_notes' => 'Addressed by modifying the proposal.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('objection_map', ['id' => $objection->id, 'status' => 'resolved']);
    }

    public function test_consensus_can_be_concluded(): void
    {
        $user = $this->actingAsUser();
        $consensus = ConsensusModel::factory()->consensusReached()->create();

        $response = $this->post("/consensus/{$consensus->id}/conclude");

        $response->assertRedirect();
        $this->assertDatabaseHas('consensus_models', [
            'id' => $consensus->id,
            'outcome' => 'consensus_reached',
        ]);
    }
}
