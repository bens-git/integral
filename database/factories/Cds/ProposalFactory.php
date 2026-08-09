<?php

namespace Database\Factories\Cds;

use App\Models\Cds\Participant;
use App\Models\Cds\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

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
        ];
    }
}
