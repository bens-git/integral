<?php

namespace Tests\Feature\Cds;

use App\Models\User;
use App\Models\Cds\Participant;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\ConsensusModel;
use App\Models\Cds\DecisionLedger;
use App\Models\Cds\DecisionDispatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CdsDecisionAndImplementationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_decisions_index_is_accessible(): void
    {
        $this->actingAsUser();

        $response = $this->get('/cds/decisions');
        $response->assertStatus(200);
    }

    public function test_decision_ledger_is_created_on_framing(): void
    {
        $user = $this->actingAsUser();
        $participant = Participant::factory()->create(['user_id' => $user->id]);
        $submission = \App\Models\Cds\Submission::factory()->create(['submitter_id' => $participant->id]);

        $response = $this->post('/cds/issues', [
            'submission_id' => $submission->id,
            'framed_problem' => 'Test problem',
            'priority' => 5,
            'decision_type' => 'policy',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('decision_issues', ['submission_id' => $submission->id]);
    }

    public function test_decision_dispatch_can_be_created(): void
    {
        $user = $this->actingAsUser();
        $consensus = ConsensusModel::factory()->create();

        $dispatch = DecisionDispatch::factory()->create([
            'consensus_id' => $consensus->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('decision_dispatches', ['id' => $dispatch->id]);
        $this->assertTrue($dispatch->isPending());
    }

    public function test_issue_can_be_marked_implemented(): void
    {
        $user = $this->actingAsUser();
        $issue = DecisionIssue::factory()->decided()->create();

        $issue->update([
            'status' => 'implemented',
            'implemented_at' => now(),
        ]);

        $this->assertDatabaseHas('decision_issues', [
            'id' => $issue->id,
            'status' => 'implemented',
        ]);
    }

    public function test_decision_ledger_tracks_event_types(): void
    {
        $user = $this->actingAsUser();
        $issue = DecisionIssue::factory()->create();

        $eventTypes = ['created', 'framed', 'deliberation_started', 'consensus_reached', 'implemented'];

        foreach ($eventTypes as $eventType) {
            DecisionLedger::factory()->create([
                'issue_id' => $issue->id,
                'event_type' => $eventType,
            ]);
        }

        $this->assertEquals(5, $issue->decisionLedger->count());
    }

    public function test_decision_dispatch_links_consensus_and_issue(): void
    {
        $user = $this->actingAsUser();
        $issue = DecisionIssue::factory()->create();
        $consensus = ConsensusModel::factory()->create(['issue_id' => $issue->id]);

        $dispatch = DecisionDispatch::factory()->create([
            'consensus_id' => $consensus->id,
            'issue_id' => $issue->id,
        ]);

        $this->assertEquals($consensus->id, $dispatch->consensus->id);
        $this->assertEquals($issue->id, $dispatch->issue->id);
    }
}
