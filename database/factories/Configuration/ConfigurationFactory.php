<?php

namespace Database\Factories\Configuration;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\Configuration>
 */
class ConfigurationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tkp_version' => \App\Models\Tkp\Tkp::factory(),
            'image' => null,
            'saved_schema' => [
                'nodes' => [],
                'connections' => [],
                'other' => [],
                'page' => [
                    'width' => 600,
                    'height' => 600,
                ],
            ]
        ];
    }
}
