<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\DecisionLedger;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\ConsensusModel;
use App\Models\Cds\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DecisionLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_decision_ledger_can_be_created(): void
    {
        $issue = DecisionIssue::factory()->create();
        $ledger = DecisionLedger::factory()->create(['issue_id' => $issue->id]);

        $this->assertDatabaseHas('decision_ledger', ['id' => $ledger->id]);
        $this->assertEquals($issue->id, $ledger->issue_id);
    }

    public function test_decision_ledger_belongs_to_issue(): void
    {
        $issue = DecisionIssue::factory()->create();
        $ledger = DecisionLedger::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(DecisionIssue::class, $ledger->issue);
    }

    public function test_decision_ledger_belongs_to_consensus(): void
    {
        $consensus = ConsensusModel::factory()->create();
        $ledger = DecisionLedger::factory()->create(['consensus_id' => $consensus->id]);

        $this->assertInstanceOf(ConsensusModel::class, $ledger->consensus);
    }

    public function test_decision_ledger_belongs_to_participant(): void
    {
        $participant = Participant::factory()->create();
        $ledger = DecisionLedger::factory()->create(['participant_id' => $participant->id]);

        $this->assertInstanceOf(Participant::class, $ledger->participant);
    }

    public function test_decision_ledger_generates_hash(): void
    {
        $issue = DecisionIssue::factory()->create();
        $ledger = DecisionLedger::factory()->create(['issue_id' => $issue->id]);
        $hash = $ledger->generateHash();

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function test_decision_ledger_is_verified_method(): void
    {
        $ledger = new DecisionLedger(['is_verified' => true]);
        $this->assertTrue($ledger->isVerified());

        $ledger->is_verified = false;
        $this->assertFalse($ledger->isVerified());
    }
}
