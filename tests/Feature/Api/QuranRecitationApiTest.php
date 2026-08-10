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

    public function test_lists_enabled_recitations_only(): void
    {
        QuranRecitation::factory()->create([
            'slug' => 'enabled-reciter-'.uniqid(),
            'name_en' => 'Enabled Reciter',
            'name_ar' => 'قارئ مفعّل',
        ]);

        $disabled = QuranRecitation::factory()->disabled()->create([
            'slug' => 'disabled-reciter-'.uniqid(),
            'name_en' => 'Disabled Reciter',
        ]);

        $response = $this->getJson('/api/v1/quran/recitations')
            ->assertOk()
            ->assertJsonPath('success', true);

        $slugs = collect($response->json('data'))->pluck('slug');

        $this->assertFalse($slugs->contains($disabled->slug));
    }

    public function test_returns_localized_name_in_arabic(): void
    {
        $recitation = QuranRecitation::factory()->create([
            'slug' => 'localized-reciter-'.uniqid(),
            'name_en' => 'Abdul Basit Mujawwad 128kbps',
            'name_ar' => 'عبد الباسط مجود',
        ]);

        $this->getJson('/api/v1/quran/recitations/'.$recitation->slug, [
            'Accept-Language' => 'ar',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'عبد الباسط مجود');
    }

    public function test_returns_english_name_by_default(): void
    {
        $recitation = QuranRecitation::factory()->create([
            'slug' => 'english-reciter-'.uniqid(),
            'name_en' => 'Abdul Basit Mujawwad 128kbps',
            'name_ar' => 'عبد الباسط مجود',
        ]);

        $this->getJson('/api/v1/quran/recitations/'.$recitation->slug)
            ->assertOk()
            ->assertJsonPath('data.name', 'Abdul Basit Mujawwad 128kbps');
    }

    public function test_disabled_recitation_is_not_accessible_via_api(): void
    {
        $recitation = QuranRecitation::factory()->disabled()->create([
            'slug' => 'disabled-reciter-'.uniqid(),
            'name_en' => 'Disabled Reciter',
        ]);

        $this->getJson('/api/v1/quran/recitations/'.$recitation->slug)
            ->assertNotFound();
    }

    public function test_lists_ayahs_for_recitation_and_filters_by_surah(): void
    {
        $recitation = QuranRecitation::factory()->create([
            'slug' => 'ayahs-reciter-'.uniqid(),
        ]);

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
            'file' => 'quran-recitation-ayahs/'.$recitation->slug.'/001001.zip',
        ]);

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 2,
            'ayah' => 1,
            'file' => 'quran-recitation-ayahs/'.$recitation->slug.'/002001.zip',
        ]);

        $this->getJson('/api/v1/quran/recitations/'.$recitation->slug.'/ayahs?surah=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.surah', 1)
            ->assertJsonPath('data.0.ayah', 1);
    }

    public function test_shows_single_ayah_with_file_url(): void
    {
        Storage::fake('public');

        $recitation = QuranRecitation::factory()->create([
            'slug' => 'single-ayah-reciter-'.uniqid(),
        ]);

        $filePath = 'quran-recitation-ayahs/'.$recitation->slug.'/001001.zip';

        Storage::disk('public')->put($filePath, 'zip-bytes');

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
            'file' => $filePath,
        ]);

        $response = $this->getJson('/api/v1/quran/recitations/'.$recitation->slug.'/ayahs/1/1')
            ->assertOk()
            ->assertJsonPath('data.surah', 1)
            ->assertJsonPath('data.ayah', 1);

        $this->assertStringContainsString(
            $filePath,
            (string) $response->json('data.file_url'),
        );
    }

    public function test_disabled_recitation_ayahs_are_not_accessible(): void
    {
        $recitation = QuranRecitation::factory()->disabled()->create([
            'slug' => 'disabled-ayahs-reciter-'.uniqid(),
        ]);

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
        ]);

        $this->getJson('/api/v1/quran/recitations/'.$recitation->slug.'/ayahs')
            ->assertNotFound();
    }

    public function test_returns_not_found_for_missing_recitation(): void
    {
        $this->getJson('/api/v1/quran/recitations/missing-reciter')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }
}
