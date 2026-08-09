<?php

namespace Database\Factories\Cds;

use App\Models\Cds\Participant;
use App\Models\Cds\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'submitter_id' => Participant::factory(),
            'node_id' => null,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'summary' => fake()->optional()->sentence(),
            'content' => fake()->optional()->paragraphs(3, true),
            'status' => fake()->randomElement(['draft', 'submitted', 'validated', 'framed', 'active', 'accepted', 'rejected', 'implemented', 'archived']),
            'category' => fake()->randomElement(['policy', 'resource', 'design', 'coordination', 'review']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'scope' => fake()->randomElement(['local', 'regional', 'bioregional', 'global']),
            'version' => '1',
            'supersedes_id' => null,
            'is_amendment' => false,
            'amends_id' => null,
            'embedding' => null,
            'submission_type' => 'proposal',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'submitted']);
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'validated']);
    }

    public function withEmbedding(): static
    {
        return $this->state(fn (array $attributes) => [
            'embedding' => array_fill(0, 10, fake()->randomFloat(4, -1, 1)),
        ]);
    }
}
