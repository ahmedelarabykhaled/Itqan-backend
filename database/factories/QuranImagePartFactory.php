<?php

namespace Database\Factories;

use App\Models\QuranImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuranImagePart>
 */
class QuranImagePartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quran_image_id' => QuranImage::factory(),
            'source_url' => fake()->optional()->url(),
            'file' => null,
        ];
    }
}
