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

    public function test_extracts_mp3_from_existing_zip_and_stores_path(): void
    {
        Storage::fake('public');

        $mp3Contents = 'fake-mp3-bytes';
        $zipRelativePath = 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip';
        $mp3RelativePath = 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.mp3';

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
            'slug' => 'unzip-reciter-'.uniqid(),
        ]);

        $ayah = QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
            'file' => $zipRelativePath,
            'mp3_file' => null,
        ]);

        $this->artisan('quran-recitations:unzip', [
            '--reciters' => [$recitation->slug],
        ])->assertSuccessful();

        $ayah->refresh();

        $this->assertSame($mp3RelativePath, $ayah->mp3_file);
        Storage::disk('public')->assertExists($mp3RelativePath);
        $this->assertSame($mp3Contents, Storage::disk('public')->get($mp3RelativePath));
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
    }
}
