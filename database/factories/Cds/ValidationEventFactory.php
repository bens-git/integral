<?php

namespace Database\Factories\Cds;

use App\Models\Cds\Participant;
use App\Models\Cds\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValidationEventFactory extends Factory
{
    protected $model = \App\Models\Cds\ValidationEvent::class;

    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'validator_id' => Participant::factory(),
            'node_id' => null,
            'result' => fake()->randomElement(['valid', 'invalid', 'needs_revision', 'pending']),
            'notes' => fake()->optional()->sentence(),
            'validation_type' => fake()->randomElement(['technical', 'ecological', 'social', 'legal', 'resource']),
            'is_blocking' => fake()->boolean(),
            'supersedes_id' => null,
        ];
    }

    public function valid(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'valid',
            'is_blocking' => false,
        ]);
    }

    public function invalid(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'invalid',
            'is_blocking' => true,
        ]);
    }

    public function needsRevision(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'needs_revision',
            'is_blocking' => false,
        ]);
    }

    public function blocking(): static
    {
        return $this->state(fn (array $attributes) => ['is_blocking' => true]);
    }
}
