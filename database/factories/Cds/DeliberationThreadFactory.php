<?php

namespace Database\Factories\Cds;

use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliberationThreadFactory extends Factory
{
    protected $model = \App\Models\Cds\DeliberationThread::class;

    public function definition(): array
    {
        return [
            'issue_id' => DecisionIssue::factory(),
            'parent_id' => null,
            'created_by_id' => Participant::factory(),
            'title' => fake()->sentence(),
            'topic' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['open', 'closed', 'archived']),
            'is_pinned' => fake()->boolean(),
            'is_locked' => fake()->boolean(),
            'message_count' => 0,
            'last_activity_at' => now(),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'open']);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'closed']);
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => ['is_pinned' => true]);
    }
}
