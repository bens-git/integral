<?php

namespace Database\Factories\Cds;

use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsensusModelFactory extends Factory
{
    protected $model = \App\Models\Cds\ConsensusModel::class;

    public function definition(): array
    {
        return [
            'issue_id' => DecisionIssue::factory(),
            'node_id' => null,
            'method' => fake()->randomElement(['weighted_consensus', 'condorcet', 'ranked_choice', 'consent']),
            'consensus_score' => null,
            'outcome' => 'pending',
            'threshold' => fake()->numberBetween(50, 100),
            'total_participants' => fake()->numberBetween(1, 20),
            'total_votes' => 0,
            'votes_strong_support' => 0,
            'votes_support' => 0,
            'votes_neutral' => 0,
            'votes_concern' => 0,
            'votes_block' => 0,
            'blocking_objections' => 0,
            'summary' => fake()->optional()->sentence(),
            'rationale' => fake()->optional()->sentence(),
            'facilitator_id' => Participant::factory()->facilitator(),
            'voting_started_at' => now(),
            'voting_ended_at' => null,
            'outcome_declared_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['outcome' => 'pending']);
    }

    public function consensusReached(): static
    {
        return $this->state(fn (array $attributes) => [
            'outcome' => 'consensus_reached',
            'consensus_score' => 85.00,
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'outcome' => 'blocked',
            'blocking_objections' => 1,
        ]);
    }
}
