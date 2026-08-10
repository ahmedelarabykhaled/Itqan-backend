<?php

namespace Database\Factories;

use App\Models\QuranRecitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuranRecitationAyah>
 */
class QuranRecitationAyahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quran_recitation_id' => QuranRecitation::factory(),
            'surah' => 1,
            'ayah' => 1,
            'source_url' => 'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps/001001.mp3',
            'file' => null,
        ];
    }
}
