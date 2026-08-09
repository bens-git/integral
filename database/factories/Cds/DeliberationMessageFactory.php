<?php

namespace Database\Factories\Cds;

use App\Models\Cds\DeliberationThread;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliberationMessageFactory extends Factory
{
    protected $model = \App\Models\Cds\DeliberationMessage::class;

    public function definition(): array
    {
        return [
            'thread_id' => DeliberationThread::factory(),
            'participant_id' => Participant::factory(),
            'parent_id' => null,
            'message' => fake()->paragraph(),
            'stance' => fake()->randomElement(['support', 'concern', 'objection', 'question', 'neutral', 'suggestion']),
            'message_type' => fake()->randomElement(['text', 'evidence', 'amendment', 'procedural']),
            'is_edited' => false,
            'edited_at' => null,
            'upvotes' => fake()->numberBetween(0, 50),
            'downvotes' => fake()->numberBetween(0, 10),
            'is_resolved' => false,
            'resolved_by_id' => null,
        ];
    }

    public function support(): static
    {
        return $this->state(fn (array $attributes) => ['stance' => 'support']);
    }

    public function objection(): static
    {
        return $this->state(fn (array $attributes) => ['stance' => 'objection']);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_resolved' => true,
            'resolved_by_id' => Participant::factory(),
        ]);
    }
}
