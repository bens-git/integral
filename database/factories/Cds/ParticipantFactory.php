<?php

namespace Database\Factories\Cds;

use App\Models\User;
use App\Models\Cds\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'node_id' => null,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['member', 'facilitator', 'admin']),
            'status' => fake()->randomElement(['active', 'inactive']),
            'email_verified_at' => now(),
            'remember_token' => null,
        ];
    }

    public function facilitator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'facilitator',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
