<?php

namespace Database\Factories\Cds;

use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use App\Models\Cds\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecisionIssueFactory extends Factory
{
    protected $model = DecisionIssue::class;

    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'node_id' => null,
            'framed_problem' => fake()->sentence(),
            'scope' => fake()->optional()->sentence(),
            'success_criteria' => fake()->optional()->sentence(),
            'constraints' => fake()->optional()->sentence(),
            'priority' => fake()->numberBetween(1, 10),
            'status' => fake()->randomElement(['draft', 'framing', 'deliberation', 'consensus', 'decided', 'implemented', 'archived']),
            'decision_type' => fake()->randomElement(['policy', 'resource_allocation', 'design_approval', 'coordination', 'review']),
            'facilitator_id' => Participant::factory()->facilitator(),
            'framing_completed_at' => null,
            'deliberation_started_at' => null,
            'consensus_reached_at' => null,
            'implemented_at' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    public function framing(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'framing']);
    }

    public function deliberation(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'deliberation']);
    }

    public function decided(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'decided']);
    }

    public function implemented(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'implemented',
            'implemented_at' => now(),
        ]);
    }
}
