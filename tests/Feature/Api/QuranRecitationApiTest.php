<?php

namespace Tests\Feature\Api;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuranRecitationApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_lists_recitations(): void
    {
        QuranRecitation::factory()->create([
            'slug' => 'Abdul_Basit_Mujawwad_128kbps',
            'name' => 'Abdul Basit Mujawwad 128kbps',
        ]);

        $this->getJson('/api/v1/quran/recitations')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.slug', 'Abdul_Basit_Mujawwad_128kbps');
    }

    public function test_shows_recitation_by_slug(): void
    {
        QuranRecitation::factory()->create([
            'slug' => 'Abdul_Basit_Mujawwad_128kbps',
            'name' => 'Abdul Basit Mujawwad 128kbps',
        ]);

        $this->getJson('/api/v1/quran/recitations/Abdul_Basit_Mujawwad_128kbps')
            ->assertOk()
            ->assertJsonPath('data.name', 'Abdul Basit Mujawwad 128kbps');
    }

    public function test_lists_ayahs_for_recitation_and_filters_by_surah(): void
    {
        $recitation = QuranRecitation::factory()->create([
            'slug' => 'Abdul_Basit_Mujawwad_128kbps',
        ]);

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
            'file' => 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip',
        ]);

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 2,
            'ayah' => 1,
            'file' => 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/002001.zip',
        ]);

        $this->getJson('/api/v1/quran/recitations/Abdul_Basit_Mujawwad_128kbps/ayahs?surah=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.surah', 1)
            ->assertJsonPath('data.0.ayah', 1);
    }

    public function test_shows_single_ayah_with_file_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip',
            'zip-bytes',
        );

        $recitation = QuranRecitation::factory()->create([
            'slug' => 'Abdul_Basit_Mujawwad_128kbps',
        ]);

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
            'file' => 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip',
        ]);

        $response = $this->getJson('/api/v1/quran/recitations/Abdul_Basit_Mujawwad_128kbps/ayahs/1/1')
            ->assertOk()
            ->assertJsonPath('data.surah', 1)
            ->assertJsonPath('data.ayah', 1);

        $this->assertStringContainsString(
            'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip',
            (string) $response->json('data.file_url'),
        );
    }

    public function test_returns_not_found_for_missing_recitation(): void
    {
        $this->getJson('/api/v1/quran/recitations/missing-reciter')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }
}
