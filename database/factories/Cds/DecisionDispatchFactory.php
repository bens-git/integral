<?php

namespace Database\Factories\Cds;

use App\Models\Cds\ConsensusModel;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecisionDispatchFactory extends Factory
{
    protected $model = \App\Models\Cds\DecisionDispatch::class;

    public function definition(): array
    {
        return [
            'consensus_id' => ConsensusModel::factory(),
            'issue_id' => DecisionIssue::factory(),
            'node_id' => null,
            'target_system' => fake()->randomElement(['cos', 'oad', 'itc', 'frs', 'external']),
            'action_type' => fake()->randomElement(['create_project', 'allocate_resources', 'update_design', 'trigger_review', 'notify']),
            'payload_summary' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'failed', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'dispatched_by_id' => Participant::factory(),
            'completed_by_id' => null,
            'result_notes' => null,
            'error_message' => null,
            'retry_count' => 0,
            'dispatched_at' => null,
            'completed_at' => null,
            'next_retry_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'dispatched_at' => now(),
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'error_message' => fake()->sentence(),
        ]);
    }
}
