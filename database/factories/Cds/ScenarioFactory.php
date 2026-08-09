<?php

namespace Database\Factories\Cds;

use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioFactory extends Factory
{
    protected $model = \App\Models\Cds\Scenario::class;

    public function definition(): array
    {
        return [
            'issue_id' => DecisionIssue::factory(),
            'created_by_id' => Participant::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'assumptions' => fake()->optional()->sentence(),
            'methodology' => fake()->optional()->sentence(),
            'viability_score' => fake()->randomFloat(2, 0, 100),
            'risk_score' => fake()->randomFloat(2, 0, 100),
            'impact_score' => fake()->randomFloat(2, 0, 100),
            'status' => fake()->randomElement(['draft', 'modeling', 'complete', 'archived']),
            'based_on_id' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    public function complete(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'complete']);
    }
}
