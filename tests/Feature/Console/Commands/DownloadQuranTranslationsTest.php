<?php

namespace Tests\Feature\Console\Commands;

use App\Models\QuranTranslation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadQuranTranslationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_downloads_translations_and_stores_files_as_is(): void
    {
        Storage::fake('public');

        Http::fake([
            'https://quran.app/data/translations.php?v=25' => Http::response([
                'data' => [
                    [
                        'id' => 58,
                        'displayName' => 'Arabic Ibn Kathir Tafseer',
                        'languageCode' => 'ar',
                        'fileUrl' => 'https://android.quran.com/data/getTranslation.php?id=58&ext=zip',
                        'fileName' => 'quran.ar.ibnkathir.db',
                        'saveTo' => 'databases',
                        'downloadType' => 'translation',
                        'last_modified' => '2023-01-17 21:17:11',
                        'minimumVersion' => 5,
                        'currentVersion' => 16,
                        'translatorForeign' => 'تفسير ابن كثير',
                    ],
                    [
                        'id' => 36,
                        'displayName' => 'English Translation (Yusuf Ali)',
                        'translator' => 'Yusuf Ali',
                        'languageCode' => 'en',
                        'fileUrl' => 'https://android.quran.com/data/getTranslation.php?id=36',
                        'fileName' => 'quran.en.yusufali.db',
                        'saveTo' => 'databases',
                        'downloadType' => 'translation',
                        'last_modified' => '2023-01-17 21:17:11',
                        'minimumVersion' => 2,
                        'currentVersion' => 8,
                    ],
                ],
            ], 200),
            'https://android.quran.com/data/getTranslation.php?id=58&ext=zip' => Http::response('zip-bytes', 200),
            'https://android.quran.com/data/getTranslation.php?id=36' => Http::response('db-bytes', 200),
        ]);

        $this->artisan('quran-translations:download', [
            '--concurrency' => 2,
        ])->assertSuccessful();

        $zipTranslation = QuranTranslation::query()->where('external_id', 58)->first();
        $dbTranslation = QuranTranslation::query()->where('external_id', 36)->first();

        $this->assertNotNull($zipTranslation);
        $this->assertSame('Arabic Ibn Kathir Tafseer', $zipTranslation->display_name);
        $this->assertSame('ar', $zipTranslation->language_code);
        $this->assertSame(16, $zipTranslation->current_version);
        $this->assertSame('quran-translations/58/quran.ar.ibnkathir.db.zip', $zipTranslation->file);
        Storage::disk('public')->assertExists('quran-translations/58/quran.ar.ibnkathir.db.zip');
        $this->assertSame('zip-bytes', Storage::disk('public')->get('quran-translations/58/quran.ar.ibnkathir.db.zip'));

        $this->assertNotNull($dbTranslation);
        $this->assertSame('English Translation (Yusuf Ali)', $dbTranslation->display_name);
        $this->assertSame('quran-translations/36/quran.en.yusufali.db', $dbTranslation->file);
        Storage::disk('public')->assertExists('quran-translations/36/quran.en.yusufali.db');
        $this->assertSame('db-bytes', Storage::disk('public')->get('quran-translations/36/quran.en.yusufali.db'));
    }

    public function test_skips_existing_files_unless_forced(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('quran-translations/58/quran.ar.ibnkathir.db.zip', 'existing');

        QuranTranslation::factory()->create([
            'external_id' => 58,
            'display_name' => 'Arabic Ibn Kathir Tafseer',
            'language_code' => 'ar',
            'file_url' => 'https://android.quran.com/data/getTranslation.php?id=58&ext=zip',
            'file_name' => 'quran.ar.ibnkathir.db',
            'file' => 'quran-translations/58/quran.ar.ibnkathir.db.zip',
        ]);

        Http::fake([
            'https://quran.app/data/translations.php?v=25' => Http::response([
                'data' => [
                    [
                        'id' => 58,
                        'displayName' => 'Arabic Ibn Kathir Tafseer',
                        'languageCode' => 'ar',
                        'fileUrl' => 'https://android.quran.com/data/getTranslation.php?id=58&ext=zip',
                        'fileName' => 'quran.ar.ibnkathir.db',
                        'saveTo' => 'databases',
                        'downloadType' => 'translation',
                        'minimumVersion' => 5,
                        'currentVersion' => 16,
                    ],
                ],
            ], 200),
        ]);

        $this->artisan('quran-translations:download', [
            '--id' => [58],
        ])->assertSuccessful();

        Http::assertSentCount(1);
        $this->assertSame('existing', Storage::disk('public')->get('quran-translations/58/quran.ar.ibnkathir.db.zip'));
    }

    public function test_force_redownloads_existing_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('quran-translations/58/quran.ar.ibnkathir.db.zip', 'existing');

        QuranTranslation::factory()->create([
            'external_id' => 58,
            'display_name' => 'Arabic Ibn Kathir Tafseer',
            'language_code' => 'ar',
            'file_url' => 'https://android.quran.com/data/getTranslation.php?id=58&ext=zip',
            'file_name' => 'quran.ar.ibnkathir.db',
            'file' => 'quran-translations/58/quran.ar.ibnkathir.db.zip',
        ]);

        Http::fake([
            'https://quran.app/data/translations.php?v=25' => Http::response([
                'data' => [
                    [
                        'id' => 58,
                        'displayName' => 'Arabic Ibn Kathir Tafseer',
                        'languageCode' => 'ar',
                        'fileUrl' => 'https://android.quran.com/data/getTranslation.php?id=58&ext=zip',
                        'fileName' => 'quran.ar.ibnkathir.db',
                        'saveTo' => 'databases',
                        'downloadType' => 'translation',
                        'minimumVersion' => 5,
                        'currentVersion' => 16,
                    ],
                ],
            ], 200),
            'https://android.quran.com/data/getTranslation.php?id=58&ext=zip' => Http::response('fresh-zip-bytes', 200),
        ]);

        $this->artisan('quran-translations:download', [
            '--id' => [58],
            '--force' => true,
        ])->assertSuccessful();

        $this->assertSame('fresh-zip-bytes', Storage::disk('public')->get('quran-translations/58/quran.ar.ibnkathir.db.zip'));
    }

    public function test_continues_when_individual_download_fails(): void
    {
        Storage::fake('public');

        Http::fake([
            'https://quran.app/data/translations.php?v=25' => Http::response([
                'data' => [
                    [
                        'id' => 58,
                        'displayName' => 'Arabic Ibn Kathir Tafseer',
                        'languageCode' => 'ar',
                        'fileUrl' => 'https://android.quran.com/data/getTranslation.php?id=58&ext=zip',
                        'fileName' => 'quran.ar.ibnkathir.db',
                        'saveTo' => 'databases',
                        'downloadType' => 'translation',
                        'minimumVersion' => 5,
                        'currentVersion' => 16,
                    ],
                    [
                        'id' => 36,
                        'displayName' => 'English Translation (Yusuf Ali)',
                        'languageCode' => 'en',
                        'fileUrl' => 'https://android.quran.com/data/getTranslation.php?id=36',
                        'fileName' => 'quran.en.yusufali.db',
                        'saveTo' => 'databases',
                        'downloadType' => 'translation',
                        'minimumVersion' => 2,
                        'currentVersion' => 8,
                    ],
                ],
            ], 200),
            'https://android.quran.com/data/getTranslation.php?id=58&ext=zip' => Http::response('', 500),
            'https://android.quran.com/data/getTranslation.php?id=36' => Http::response('db-bytes', 200),
        ]);

        $this->artisan('quran-translations:download', [
            '--concurrency' => 2,
        ])->assertSuccessful();

        $failedTranslation = QuranTranslation::query()->where('external_id', 58)->first();
        $successfulTranslation = QuranTranslation::query()->where('external_id', 36)->first();

        $this->assertNotNull($failedTranslation);
        $this->assertNull($failedTranslation->file);

        $this->assertNotNull($successfulTranslation);
        $this->assertSame('quran-translations/36/quran.en.yusufali.db', $successfulTranslation->file);
        Storage::disk('public')->assertExists('quran-translations/36/quran.en.yusufali.db');
    }
}
