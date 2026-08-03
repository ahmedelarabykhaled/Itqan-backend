<?php

namespace Tests\Feature\Console\Commands;

use App\Models\QuranImage;
use App\Models\QuranImagePart;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class DownloadQuranImagesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_downloads_ayahinfo_and_converts_pages_to_zip(): void
    {
        Storage::fake('public');

        $pngContents = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );

        Http::fake([
            'https://files.quran.app/hafs/madani/databases/ayahinfo/ayahinfo_320.zip' => Http::response('ayahinfo-zip-bytes', 200),
            'https://files.quran.app/hafs/madani/width_320/page001.png' => Http::response($pngContents, 200, [
                'Content-Type' => 'image/png',
            ]),
            'https://files.quran.app/hafs/madani/width_320/page002.png' => Http::response($pngContents, 200, [
                'Content-Type' => 'image/png',
            ]),
        ]);

        $this->artisan('quran-images:download', [
            '--widths' => [320],
            '--pages' => 2,
            '--concurrency' => 2,
        ])->assertSuccessful();

        $image = QuranImage::query()->where('width', 320)->first();

        $this->assertNotNull($image);
        $this->assertSame(
            'https://files.quran.app/hafs/madani/databases/ayahinfo/ayahinfo_320.zip',
            $image->source_url,
        );
        $this->assertSame('quran-images/ayahinfo_320.zip', $image->file);
        Storage::disk('public')->assertExists('quran-images/ayahinfo_320.zip');
        $this->assertSame('ayahinfo-zip-bytes', Storage::disk('public')->get('quran-images/ayahinfo_320.zip'));

        $this->assertSame(2, $image->parts()->count());

        $part = QuranImagePart::query()
            ->where('quran_image_id', $image->id)
            ->where('source_url', 'https://files.quran.app/hafs/madani/width_320/page001.png')
            ->first();

        $this->assertNotNull($part);
        $this->assertSame('quran-image-parts/320/page001.zip', $part->file);
        Storage::disk('public')->assertExists('quran-image-parts/320/page001.zip');

        $tempZip = tempnam(sys_get_temp_dir(), 'assert-zip-');
        $this->assertNotFalse($tempZip);
        file_put_contents($tempZip, Storage::disk('public')->get('quran-image-parts/320/page001.zip'));

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip));
        $this->assertSame(1, $zip->numFiles);
        $this->assertSame('page001.png', $zip->getNameIndex(0));
        $this->assertSame($pngContents, $zip->getFromName('page001.png'));
        $zip->close();
        @unlink($tempZip);
    }

    public function test_skips_existing_files_unless_forced(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('quran-images/ayahinfo_320.zip', 'existing');

        $image = QuranImage::factory()->create([
            'width' => 320,
            'source_url' => 'https://files.quran.app/hafs/madani/databases/ayahinfo/ayahinfo_320.zip',
            'file' => 'quran-images/ayahinfo_320.zip',
        ]);

        Http::fake();

        $this->artisan('quran-images:download', [
            '--widths' => [320],
            '--skip-parts' => true,
        ])->assertSuccessful();

        Http::assertNothingSent();
        $this->assertSame('existing', Storage::disk('public')->get('quran-images/ayahinfo_320.zip'));
        $this->assertTrue($image->is(QuranImage::query()->where('width', 320)->first()));
    }
}
