<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuranTranslation>
 */
class QuranTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $externalId = fake()->unique()->numberBetween(1, 999);

        return [
            'external_id' => $externalId,
            'display_name' => fake()->words(3, true),
            'translator' => fake()->optional()->name(),
            'translator_foreign' => fake()->optional()->name(),
            'language_code' => fake()->randomElement(['ar', 'en', 'fr', 'tr']),
            'file_url' => "https://android.quran.com/data/getTranslation.php?id={$externalId}",
            'file_name' => "quran.{$externalId}.db",
            'save_to' => 'databases',
            'download_type' => 'translation',
            'minimum_version' => fake()->numberBetween(1, 5),
            'current_version' => fake()->numberBetween(1, 20),
            'remote_last_modified' => fake()->optional()->dateTime(),
            'file' => null,
        ];
    }
}
