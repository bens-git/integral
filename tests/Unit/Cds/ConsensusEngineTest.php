<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\ConsensusModel;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Objection;
use App\Services\Cds\ConsensusEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsensusEngineTest extends TestCase
{
    use RefreshDatabase;

    protected ConsensusEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new ConsensusEngine();
    }

    public function test_calculate_score_with_zero_votes(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'total_votes' => 0,
        ]);

        $score = $this->engine->calculateScore($consensus);
        $this->assertEquals(0.0, $score);
    }

    public function test_calculate_score_with_votes(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'votes_strong_support' => 2,
            'votes_support' => 3,
            'votes_neutral' => 1,
            'votes_concern' => 1,
            'votes_block' => 0,
            'total_votes' => 7,
        ]);

        $score = $this->engine->calculateScore($consensus);
        $this->assertEqualsWithDelta(72.86, $score, 0.01);
    }

    public function test_has_consensus_returns_true_when_threshold_met(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'consensus_score' => 85.00,
            'threshold' => 75,
            'blocking_objections' => 0,
        ]);

        $this->assertTrue($this->engine->hasConsensus($consensus));
    }

    public function test_has_consensus_returns_false_when_below_threshold(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'consensus_score' => 50.00,
            'threshold' => 75,
            'blocking_objections' => 0,
        ]);

        $this->assertFalse($this->engine->hasConsensus($consensus));
    }

    public function test_has_consensus_returns_false_with_blocking_objections(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'consensus_score' => 85.00,
            'threshold' => 75,
            'blocking_objections' => 1,
        ]);

        $this->assertFalse($this->engine->hasConsensus($consensus));
    }

    public function test_determine_outcome_consensus_reached(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'consensus_score' => 85.00,
            'threshold' => 75,
            'blocking_objections' => 0,
            'method' => 'weighted_consensus',
        ]);

        $this->assertEquals('consensus_reached', $this->engine->determineOutcome($consensus));
    }

    public function test_determine_outcome_blocked(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'consensus_score' => 85.00,
            'threshold' => 75,
            'blocking_objections' => 1,
            'method' => 'weighted_consensus',
        ]);

        $this->assertEquals('blocked', $this->engine->determineOutcome($consensus));
    }

    public function test_determine_outcome_consent(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'method' => 'consent',
            'votes_block' => 0,
            'votes_concern' => 0,
        ]);

        $this->assertEquals('consent', $this->engine->determineOutcome($consensus));
    }

    public function test_determine_outcome_pending(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'consensus_score' => 50.00,
            'threshold' => 75,
            'total_participants' => 10,
            'total_votes' => 5,
            'blocking_objections' => 0,
            'method' => 'weighted_consensus',
        ]);

        $this->assertEquals('pending', $this->engine->determineOutcome($consensus));
    }

    public function test_get_breakdown_with_zero_votes(): void
    {
        $consensus = ConsensusModel::factory()->create(['total_votes' => 0]);
        $breakdown = $this->engine->getBreakdown($consensus);

        $this->assertEquals([
            'strong_support' => 0,
            'support' => 0,
            'neutral' => 0,
            'concern' => 0,
            'block' => 0,
        ], $breakdown);
    }

    public function test_get_breakdown_with_votes(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'votes_strong_support' => 2,
            'votes_support' => 3,
            'votes_neutral' => 1,
            'votes_concern' => 1,
            'votes_block' => 0,
            'total_votes' => 7,
        ]);

        $breakdown = $this->engine->getBreakdown($consensus);

        $this->assertEqualsWithDelta(28.57, $breakdown['strong_support'], 0.01);
        $this->assertEqualsWithDelta(42.86, $breakdown['support'], 0.01);
        $this->assertEqualsWithDelta(14.29, $breakdown['neutral'], 0.01);
        $this->assertEqualsWithDelta(14.29, $breakdown['concern'], 0.01);
        $this->assertEquals(0, $breakdown['block']);
    }

    public function test_get_status_summary_returns_expected_keys(): void
    {
        $consensus = ConsensusModel::factory()->create([
            'consensus_score' => 85.00,
            'threshold' => 75,
            'total_votes' => 7,
            'total_participants' => 10,
            'blocking_objections' => 0,
            'outcome' => 'consensus_reached',
        ]);

        $summary = $this->engine->getStatusSummary($consensus);

        $this->assertArrayHasKey('score', $summary);
        $this->assertArrayHasKey('threshold', $summary);
        $this->assertArrayHasKey('total_votes', $summary);
        $this->assertArrayHasKey('total_participants', $summary);
        $this->assertArrayHasKey('blocking_objections', $summary);
        $this->assertArrayHasKey('outcome', $summary);
        $this->assertArrayHasKey('has_consensus', $summary);
        $this->assertArrayHasKey('breakdown', $summary);
        $this->assertArrayHasKey('participation_rate', $summary);
        $this->assertTrue($summary['has_consensus']);
        $this->assertEqualsWithDelta(70.0, $summary['participation_rate'], 0.01);
    }
}
