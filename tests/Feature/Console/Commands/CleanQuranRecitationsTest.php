<?php

namespace Tests\Feature\Console\Commands;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanQuranRecitationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_cleans_directories_of_deleted_reciters_from_storage(): void
    {
        Storage::fake('public');

        $deletedSlug = 'deleted_reciter_128kbps';
        $activeSlug = 'active_reciter_128kbps';

        $activeReciter = QuranRecitation::factory()->create([
            'slug' => $activeSlug,
        ]);

        Storage::disk('public')->put("quran-recitation-ayahs/{$deletedSlug}/001001.mp3", 'dummy-audio-1');
        Storage::disk('public')->put("quran-recitation-ayahs/{$deletedSlug}/001002.mp3", 'dummy-audio-2');
        Storage::disk('public')->put("quran-recitation-ayahs/{$activeSlug}/001001.mp3", 'active-audio');

        $this->artisan('quran-recitations:clean')
            ->expectsOutputToContain('Scanning 2 reciter directory(ies)')
            ->expectsOutputToContain("Deleted reciter folder: quran-recitation-ayahs/{$deletedSlug} (2 files)")
            ->assertSuccessful();

        Storage::disk('public')->assertMissing("quran-recitation-ayahs/{$deletedSlug}");
        Storage::disk('public')->assertExists("quran-recitation-ayahs/{$activeSlug}/001001.mp3");
    }

    public function test_dry_run_does_not_delete_directories_or_files(): void
    {
        Storage::fake('public');

        $deletedSlug = 'dry_run_reciter_128kbps';

        Storage::disk('public')->put("quran-recitation-ayahs/{$deletedSlug}/001001.mp3", 'dummy-audio');

        $this->artisan('quran-recitations:clean', ['--dry-run' => true])
            ->expectsOutputToContain('[DRY RUN] Would delete reciter folder')
            ->assertSuccessful();

        Storage::disk('public')->assertExists("quran-recitation-ayahs/{$deletedSlug}/001001.mp3");
    }

    public function test_filters_by_specific_reciters_option(): void
    {
        Storage::fake('public');

        $deletedSlug1 = 'deleted_reciter_one';
        $deletedSlug2 = 'deleted_reciter_two';

        Storage::disk('public')->put("quran-recitation-ayahs/{$deletedSlug1}/001001.mp3", 'dummy-audio-1');
        Storage::disk('public')->put("quran-recitation-ayahs/{$deletedSlug2}/001001.mp3", 'dummy-audio-2');

        $this->artisan('quran-recitations:clean', [
            '--reciters' => [$deletedSlug1],
        ])->assertSuccessful();

        Storage::disk('public')->assertMissing("quran-recitation-ayahs/{$deletedSlug1}");
        Storage::disk('public')->assertExists("quran-recitation-ayahs/{$deletedSlug2}/001001.mp3");
    }

    public function test_cleans_orphaned_files_in_existing_reciters_when_all_orphans_flag_passed(): void
    {
        Storage::fake('public');

        $slug = 'existing_reciter_with_orphans';
        $reciter = QuranRecitation::factory()->create([
            'slug' => $slug,
        ]);

        $validMp3 = "quran-recitation-ayahs/{$slug}/001001.mp3";
        $orphanedMp3 = "quran-recitation-ayahs/{$slug}/001999.mp3";

        Storage::disk('public')->put($validMp3, 'valid-ayah');
        Storage::disk('public')->put($orphanedMp3, 'orphan-ayah');

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $reciter->id,
            'surah' => 1,
            'ayah' => 1,
            'mp3_file' => $validMp3,
            'file' => null,
        ]);

        $this->artisan('quran-recitations:clean', [
            '--all-orphans' => true,
        ])
            ->expectsOutputToContain("Deleted orphaned file: {$orphanedMp3}")
            ->assertSuccessful();

        Storage::disk('public')->assertExists($validMp3);
        Storage::disk('public')->assertMissing($orphanedMp3);
    }

    public function test_handles_empty_storage_directory_gracefully(): void
    {
        Storage::fake('public');

        $this->artisan('quran-recitations:clean')
            ->expectsOutput('No recitations storage directory found.')
            ->assertSuccessful();
    }

    public function test_model_deleted_event_removes_reciter_directory(): void
    {
        Storage::fake('public');

        $slug = 'model_event_test_reciter';
        $reciter = QuranRecitation::factory()->create([
            'slug' => $slug,
        ]);

        $filePath = "quran-recitation-ayahs/{$slug}/001001.mp3";
        Storage::disk('public')->put($filePath, 'audio-data');

        Storage::disk('public')->assertExists($filePath);

        $reciter->delete();

        Storage::disk('public')->assertMissing("quran-recitation-ayahs/{$slug}");
    }
}
