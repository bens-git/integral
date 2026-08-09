<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\DecisionDispatch;
use App\Models\Cds\ConsensusModel;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DecisionDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_decision_dispatch_can_be_created(): void
    {
        $consensus = ConsensusModel::factory()->create();
        $dispatch = DecisionDispatch::factory()->create([
            'consensus_id' => $consensus->id,
        ]);

        $this->assertDatabaseHas('decision_dispatches', ['id' => $dispatch->id]);
        $this->assertEquals($consensus->id, $dispatch->consensus_id);
    }

    public function test_decision_dispatch_belongs_to_consensus(): void
    {
        $consensus = ConsensusModel::factory()->create();
        $dispatch = DecisionDispatch::factory()->create(['consensus_id' => $consensus->id]);

        $this->assertInstanceOf(ConsensusModel::class, $dispatch->consensus);
    }

    public function test_decision_dispatch_belongs_to_issue(): void
    {
        $issue = DecisionIssue::factory()->create();
        $dispatch = DecisionDispatch::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(DecisionIssue::class, $dispatch->issue);
    }

    public function test_decision_dispatch_status_methods(): void
    {
        $dispatch = new DecisionDispatch(['status' => 'pending']);
        $this->assertTrue($dispatch->isPending());

        $dispatch->status = 'processing';
        $this->assertTrue($dispatch->isProcessing());

        $dispatch->status = 'completed';
        $this->assertTrue($dispatch->isCompleted());

        $dispatch->status = 'failed';
        $this->assertTrue($dispatch->isFailed());

        $dispatch->status = 'cancelled';
        $this->assertTrue($dispatch->isCancelled());
    }

    public function test_decision_dispatch_should_retry_when_failed_and_past_retry(): void
    {
        $dispatch = new DecisionDispatch([
            'status' => 'failed',
            'retry_count' => 1,
            'next_retry_at' => now()->subDay(),
        ]);

        $this->assertTrue($dispatch->shouldRetry());
    }

    public function test_decision_dispatch_should_not_retry_when_max_retries_reached(): void
    {
        $dispatch = new DecisionDispatch([
            'status' => 'failed',
            'retry_count' => 3,
            'next_retry_at' => now()->subDay(),
        ]);

        $this->assertFalse($dispatch->shouldRetry());
    }
}
