<?php

namespace Database\Factories\Cds;

use App\Models\Cds\ConsensusModel;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecisionLedgerFactory extends Factory
{
    protected $model = \App\Models\Cds\DecisionLedger::class;

    public function definition(): array
    {
        return [
            'issue_id' => DecisionIssue::factory(),
            'consensus_id' => ConsensusModel::factory(),
            'node_id' => null,
            'participant_id' => Participant::factory(),
            'event_type' => fake()->randomElement(['created', 'framed', 'deliberation_started', 'vote_started', 'consensus_reached', 'blocked', 'implemented', 'revised', 'archived']),
            'title' => fake()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'hash' => null,
            'previous_hash_id' => null,
            'signature' => null,
            'is_verified' => false,
            'verified_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function consensusReached(): static
    {
        return $this->state(fn (array $attributes) => ['event_type' => 'consensus_reached']);
    }
}
