<?php

namespace Database\Factories\Tkp;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tkp\Tkp>
 */
class TkpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tkp_version' => fake()->randomNumber(),
            'user_id' => \App\Models\User::factory(),
            'update_user_id' => \App\Models\User::factory(),
            'project_name' => fake()->name(),
            'client_name' => fake()->name(),
            'contract_owner' => fake()->name(),
            'implementation_object' => fake()->sentence(),
            'industry' => fake()->word(),
            'delivery_params' => [],
            //'pay_params' => [],
            'comments' => fake()->paragraph(),
        ];
    }
}
