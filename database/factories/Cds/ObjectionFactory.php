<?php

namespace Database\Factories\Cds;

use App\Models\Cds\ConsensusModel;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObjectionFactory extends Factory
{
    protected $model = \App\Models\Cds\Objection::class;

    public function definition(): array
    {
        return [
            'consensus_id' => ConsensusModel::factory(),
            'participant_id' => Participant::factory(),
            'node_id' => null,
            'objection_strength' => fake()->randomFloat(2, 0, 5),
            'objection_type' => fake()->randomElement(['principled', 'practical', 'procedural', 'technical']),
            'reason' => fake()->sentence(),
            'proposed_resolution' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['open', 'addressed', 'resolved', 'withdrawn', 'upheld']),
            'is_blocking' => fake()->boolean(),
            'addressed_by_id' => null,
            'resolution_notes' => null,
            'addressed_at' => null,
            'resolved_at' => null,
        ];
    }

    public function blocking(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_blocking' => true,
            'status' => 'open',
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }
}
