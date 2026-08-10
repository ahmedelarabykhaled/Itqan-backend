<?php

namespace Tests\Feature\Console\Commands;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class DownloadQuranRecitationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_downloads_ayah_mp3_and_converts_to_zip(): void
    {
        Storage::fake('public');

        $mp3Contents = 'fake-mp3-bytes';

        Http::fake([
            'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps/001000.mp3' => Http::response('', 404),
            'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps/001001.mp3' => Http::response($mp3Contents, 200),
            'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps/001002.mp3' => Http::response($mp3Contents, 200),
        ]);

        $this->artisan('quran-recitations:download', [
            '--reciters' => ['Abdul_Basit_Mujawwad_128kbps'],
            '--surah' => 1,
            '--limit-ayahs' => 2,
            '--concurrency' => 2,
        ])->assertSuccessful();

        $recitation = QuranRecitation::query()->where('slug', 'Abdul_Basit_Mujawwad_128kbps')->first();

        $this->assertNotNull($recitation);
        $this->assertSame(
            'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps',
            $recitation->source_url,
        );
        $this->assertSame('128kbps', $recitation->bitrate);

        $this->assertSame(1, $recitation->ayahs()->count());

        $ayah = QuranRecitationAyah::query()
            ->where('quran_recitation_id', $recitation->id)
            ->where('surah', 1)
            ->where('ayah', 1)
            ->first();

        $this->assertNotNull($ayah);
        $this->assertSame('quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip', $ayah->file);
        Storage::disk('public')->assertExists('quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip');

        $tempZip = tempnam(sys_get_temp_dir(), 'assert-zip-');
        $this->assertNotFalse($tempZip);
        file_put_contents($tempZip, Storage::disk('public')->get('quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip'));

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $this->assertSame(1, $zip->numFiles);
        $this->assertSame('001001.mp3', $zip->getNameIndex(0));
        $this->assertSame($mp3Contents, $zip->getFromName('001001.mp3'));
        $zip->close();
        @unlink($tempZip);
    }

    public function test_skips_existing_files_unless_forced(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put(
            'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip',
            'existing-zip',
        );

        $recitation = QuranRecitation::query()->create([
            'slug' => 'Abdul_Basit_Mujawwad_128kbps',
            'name_en' => 'Abdul Basit Mujawwad 128kbps',
            'bitrate' => '128kbps',
            'source_url' => 'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps',
        ]);

        QuranRecitationAyah::query()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 0,
            'source_url' => 'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps/001000.mp3',
            'file' => 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001000.zip',
        ]);

        Storage::disk('public')->put(
            'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001000.zip',
            'existing-basmalah-zip',
        );

        QuranRecitationAyah::query()->create([
            'quran_recitation_id' => $recitation->id,
            'surah' => 1,
            'ayah' => 1,
            'source_url' => 'https://everyayah.com/data/Abdul_Basit_Mujawwad_128kbps/001001.mp3',
            'file' => 'quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip',
        ]);

        Http::fake();

        $this->artisan('quran-recitations:download', [
            '--reciters' => ['Abdul_Basit_Mujawwad_128kbps'],
            '--surah' => 1,
            '--limit-ayahs' => 2,
        ])->assertSuccessful();

        Http::assertNothingSent();
        $this->assertSame('existing-zip', Storage::disk('public')->get('quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001001.zip'));
        $this->assertSame('existing-basmalah-zip', Storage::disk('public')->get('quran-recitation-ayahs/Abdul_Basit_Mujawwad_128kbps/001000.zip'));
    }
}
