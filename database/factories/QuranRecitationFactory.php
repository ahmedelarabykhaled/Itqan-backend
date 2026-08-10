<?php

namespace Database\Factories;

use App\Enums\QuranRecitationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuranRecitation>
 */
class QuranRecitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = 'Abdul_Basit_Mujawwad_128kbps';

        return [
            'slug' => $slug,
            'name_en' => 'Abdul Basit Mujawwad 128kbps',
            'name_ar' => 'عبد الباسط مجود 128kbps',
            'bitrate' => '128kbps',
            'source_url' => "https://everyayah.com/data/{$slug}",
            'status' => QuranRecitationStatus::Enabled,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (): array => [
            'status' => QuranRecitationStatus::Disabled,
        ]);
    }
}
