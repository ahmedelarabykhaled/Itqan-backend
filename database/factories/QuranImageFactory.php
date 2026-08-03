<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuranImage>
 */
class QuranImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'width' => fake()->numberBetween(800, 2400),
            'source_url' => fake()->optional()->url(),
            'file' => null,
        ];
    }
}
