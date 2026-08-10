<?php

namespace Tests\Feature\Console\Commands;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class UnzipQuranRecitationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_extracts_mp3_from_existing_zip_and_keeps_zip_by_default(): void
    {
        Storage::fake('public');

        [$recitation, $ayah, $zipRelativePath, $mp3RelativePath, $mp3Contents] = $this->createZippedAyah();

        $this->artisan('quran-recitations:unzip', [
            '--reciters' => [$recitation->slug],
        ])->assertSuccessful();

        $ayah->refresh();

        $this->assertSame($mp3RelativePath, $ayah->mp3_file);
        $this->assertSame($zipRelativePath, $ayah->file);
        Storage::disk('public')->assertExists($mp3RelativePath);
        Storage::disk('public')->assertExists($zipRelativePath);
        $this->assertSame($mp3Contents, Storage::disk('public')->get($mp3RelativePath));
    }

    public function test_extracts_mp3_and_deletes_zip_when_requested(): void
    {
        Storage::fake('public');

        [$recitation, $ayah, $zipRelativePath, $mp3RelativePath, $mp3Contents] = $this->createZippedAyah();

        $this->artisan('quran-recitations:unzip', [
            '--reciters' => [$recitation->slug],
            '--delete-zip' => true,
        ])->assertSuccessful();

        $ayah->refresh();

        $this->assertSame($mp3RelativePath, $ayah->mp3_file);
        $this->assertNull($ayah->file);
        Storage::disk('public')->assertExists($mp3RelativePath);
        Storage::disk('public')->assertMissing($zipRelativePath);
        $this->assertSame($mp3Contents, Storage::disk('public')->get($mp3RelativePath));
    }

    public function test_delete_zip_removes_archive_when_mp3_already_exists(): void
    {
        Storage::fake('public');

        $zipRelativePath = 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001003.zip';
        $mp3RelativePath = 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001003.mp3';

        Storage::disk('public')->put($zipRelativePath, 'zip-bytes');
        Storage::disk('public')->put($mp3RelativePath, 'existing-mp3');

        $recitation = QuranRecitation::factory()->create([
            'slug' => 'unzip-delete-existing-'.uniqid(),
        ]);

        $ayah = QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 3,
            'file' => $zipRelativePath,
            'mp3_file' => $mp3RelativePath,
        ]);

        $this->artisan('quran-recitations:unzip', [
            '--reciters' => [$recitation->slug],
            '--delete-zip' => true,
        ])->assertSuccessful();

        $ayah->refresh();

        $this->assertNull($ayah->file);
        $this->assertSame($mp3RelativePath, $ayah->mp3_file);
        Storage::disk('public')->assertExists($mp3RelativePath);
        Storage::disk('public')->assertMissing($zipRelativePath);
        $this->assertSame('existing-mp3', Storage::disk('public')->get($mp3RelativePath));
    }

    public function test_skips_existing_mp3_unless_forced(): void
    {
        Storage::fake('public');

        $zipRelativePath = 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001002.zip';
        $mp3RelativePath = 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001002.mp3';

        Storage::disk('public')->put($zipRelativePath, 'zip-bytes');
        Storage::disk('public')->put($mp3RelativePath, 'existing-mp3');

        $recitation = QuranRecitation::factory()->create([
            'slug' => 'unzip-skip-reciter-'.uniqid(),
        ]);

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 2,
            'file' => $zipRelativePath,
            'mp3_file' => $mp3RelativePath,
        ]);

        $this->artisan('quran-recitations:unzip', [
            '--reciters' => [$recitation->slug],
        ])->assertSuccessful();

        $this->assertSame('existing-mp3', Storage::disk('public')->get($mp3RelativePath));
        Storage::disk('public')->assertExists($zipRelativePath);
    }

    /**
     * @return array{0: QuranRecitation, 1: QuranRecitationAyah, 2: string, 3: string, 4: string}
     */
    private function createZippedAyah(): array
    {
        $mp3Contents = 'fake-mp3-bytes';
        $slug = 'unzip-reciter-'.uniqid();
        $zipRelativePath = "quran-recitation-ayahs/{$slug}/001001.zip";
        $mp3RelativePath = "quran-recitation-ayahs/{$slug}/001001.mp3";

        $tempZip = tempnam(sys_get_temp_dir(), 'ayah-zip-');
        $this->assertNotFalse($tempZip);
        $zipPath = $tempZip.'.zip';
        @unlink($tempZip);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $zip->addFromString('001001.mp3', $mp3Contents);
        $zip->close();

        Storage::disk('public')->put($zipRelativePath, file_get_contents($zipPath));
        @unlink($zipPath);

        $recitation = QuranRecitation::factory()->create([
            'slug' => $slug,
        ]);

        $ayah = QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
            'file' => $zipRelativePath,
            'mp3_file' => null,
        ]);

        return [$recitation, $ayah, $zipRelativePath, $mp3RelativePath, $mp3Contents];
    }
}
