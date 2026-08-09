<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use App\Models\Cds\Submission;
use App\Models\Cds\Scenario;
use App\Models\Cds\DeliberationThread;
use App\Models\Cds\ConsensusModel;
use App\Models\Cds\DecisionLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DecisionIssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_decision_issue_can_be_created(): void
    {
        $submission = Submission::factory()->create();
        $facilitator = Participant::factory()->facilitator()->create();
        $issue = DecisionIssue::factory()->create([
            'submission_id' => $submission->id,
            'facilitator_id' => $facilitator->id,
        ]);

        $this->assertDatabaseHas('decision_issues', ['id' => $issue->id]);
        $this->assertEquals($submission->id, $issue->submission_id);
    }

    public function test_decision_issue_belongs_to_submission(): void
    {
        $submission = Submission::factory()->create();
        $issue = DecisionIssue::factory()->create(['submission_id' => $submission->id]);

        $this->assertInstanceOf(Submission::class, $issue->proposal);
    }

    public function test_decision_issue_belongs_to_facilitator(): void
    {
        $facilitator = Participant::factory()->facilitator()->create();
        $issue = DecisionIssue::factory()->create(['facilitator_id' => $facilitator->id]);

        $this->assertInstanceOf(Participant::class, $issue->facilitator);
    }

    public function test_decision_issue_has_scenarios(): void
    {
        $issue = DecisionIssue::factory()->create();
        Scenario::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $issue->scenarios);
        $this->assertEquals(1, $issue->scenarios->count());
    }

    public function test_decision_issue_has_deliberation_threads(): void
    {
        $issue = DecisionIssue::factory()->create();
        DeliberationThread::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $issue->deliberationThreads);
        $this->assertEquals(1, $issue->deliberationThreads->count());
    }

    public function test_decision_issue_has_consensus_models(): void
    {
        $issue = DecisionIssue::factory()->create();
        ConsensusModel::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $issue->consensusModels);
        $this->assertEquals(1, $issue->consensusModels->count());
    }

    public function test_decision_issue_has_decision_ledger(): void
    {
        $issue = DecisionIssue::factory()->create();
        DecisionLedger::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $issue->decisionLedger);
        $this->assertEquals(1, $issue->decisionLedger->count());
    }

    public function test_decision_issue_status_methods(): void
    {
        $issue = new DecisionIssue(['status' => 'draft']);
        $this->assertTrue($issue->isDraft());

        $issue->status = 'framing';
        $this->assertTrue($issue->isFraming());

        $issue->status = 'deliberation';
        $this->assertTrue($issue->isDeliberation());

        $issue->status = 'consensus';
        $this->assertTrue($issue->isConsensus());

        $issue->status = 'decided';
        $this->assertTrue($issue->isDecided());

        $issue->status = 'implemented';
        $this->assertTrue($issue->isImplemented());

        $issue->status = 'archived';
        $this->assertTrue($issue->isArchived());
    }
}
