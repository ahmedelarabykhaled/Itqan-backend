<?php

namespace App\Console\Commands;

use App\Models\QuranTranslation;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadQuranTranslations extends Command
{
    public const MANIFEST_URL = 'https://quran.app/data/translations.php?v=25';

    protected $signature = 'quran-translations:download
                            {--force : Re-download and overwrite existing files}
                            {--id=* : Only download specific external ids}
                            {--concurrency=5 : Concurrent file downloads per batch}';

    protected $description = 'Download Quran translation/tafsir metadata and database files from quran.app';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $concurrency = max(1, (int) $this->option('concurrency'));
        $requestedIds = $this->resolveExternalIds();

        $this->info('Fetching translation manifest...');

        $response = Http::timeout(60)
            ->withHeaders(['User-Agent' => 'ItqanQuranTranslationsDownloader/1.0'])
            ->get(self::MANIFEST_URL);

        if (! $response->successful()) {
            $this->error("Failed to fetch manifest (HTTP {$response->status()}).");

            return self::FAILURE;
        }

        $items = $response->json('data');

        if (! is_array($items)) {
            $this->error('Invalid manifest response: missing data array.');

            return self::FAILURE;
        }

        if ($requestedIds !== []) {
            $items = array_values(array_filter(
                $items,
                fn (array $item): bool => in_array((int) ($item['id'] ?? 0), $requestedIds, true),
            ));
        }

        if ($items === []) {
            $this->warn('No translations matched the requested filters.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Syncing %d translation(s)...', count($items)));

        $records = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $records[] = $this->upsertTranslation($item);
        }

        $records = array_values(array_filter($records));

        $this->downloadFiles($records, $force, $concurrency);

        $this->info('Done.');

        return self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function resolveExternalIds(): array
    {
        return array_values(array_filter(
            array_map('intval', (array) $this->option('id')),
            fn (int $id): bool => $id > 0,
        ));
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function upsertTranslation(array $item): ?QuranTranslation
    {
        $externalId = (int) ($item['id'] ?? 0);

        if ($externalId <= 0) {
            $this->warn('Skipping item with invalid external id.');

            return null;
        }

        $remoteLastModified = filled($item['last_modified'] ?? null)
            ? Carbon::parse((string) $item['last_modified'])
            : null;

        return QuranTranslation::query()->updateOrCreate(
            ['external_id' => $externalId],
            [
                'display_name' => (string) ($item['displayName'] ?? "Translation {$externalId}"),
                'translator' => $item['translator'] ?? null,
                'translator_foreign' => $item['translatorForeign'] ?? null,
                'language_code' => (string) ($item['languageCode'] ?? 'unknown'),
                'file_url' => (string) ($item['fileUrl'] ?? ''),
                'file_name' => (string) ($item['fileName'] ?? "quran.{$externalId}.db"),
                'save_to' => $item['saveTo'] ?? null,
                'download_type' => $item['downloadType'] ?? null,
                'minimum_version' => (int) ($item['minimumVersion'] ?? 0),
                'current_version' => (int) ($item['currentVersion'] ?? 0),
                'remote_last_modified' => $remoteLastModified,
            ],
        );
    }

    /**
     * @param  list<QuranTranslation>  $records
     */
    private function downloadFiles(array $records, bool $force, int $concurrency): void
    {
        $pending = array_values(array_filter(
            $records,
            fn (QuranTranslation $translation): bool => $this->shouldDownload($translation, $force),
        ));

        if ($pending === []) {
            $this->line('All translation files already exist.');

            return;
        }

        $this->info(sprintf('Downloading %d file(s)...', count($pending)));

        $bar = $this->output->createProgressBar(count($pending));
        $bar->start();

        foreach (array_chunk($pending, $concurrency) as $batch) {
            $responses = Http::pool(function (Pool $pool) use ($batch) {
                $requests = [];

                foreach ($batch as $translation) {
                    $requests[(string) $translation->external_id] = $pool
                        ->as((string) $translation->external_id)
                        ->withHeaders(['User-Agent' => 'ItqanQuranTranslationsDownloader/1.0'])
                        ->timeout(120)
                        ->get($translation->file_url);
                }

                return $requests;
            });

            foreach ($batch as $translation) {
                $this->storeFile(
                    $translation,
                    $responses[(string) $translation->external_id] ?? null,
                );

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    private function shouldDownload(QuranTranslation $translation, bool $force): bool
    {
        if ($force) {
            return true;
        }

        return ! (filled($translation->file) && Storage::disk('public')->exists($translation->file));
    }

    private function storeFile(QuranTranslation $translation, mixed $response): void
    {
        if (! $response instanceof Response || ! $response->successful()) {
            $status = $response instanceof Response ? $response->status() : 'no-response';
            $this->newLine();
            $this->error("Failed translation {$translation->external_id} (HTTP {$status}).");

            return;
        }

        $storagePath = $this->resolveStoragePath($translation);
        Storage::disk('public')->put($storagePath, $response->body());

        $translation->update([
            'file' => $storagePath,
        ]);
    }

    private function resolveStoragePath(QuranTranslation $translation): string
    {
        $storedFileName = $this->resolveStoredFileName($translation);

        return "quran-translations/{$translation->external_id}/{$storedFileName}";
    }

    private function resolveStoredFileName(QuranTranslation $translation): string
    {
        $fileName = $translation->file_name;

        if (str_contains($translation->file_url, 'ext=zip')) {
            if (str_ends_with($fileName, '.db')) {
                return $fileName.'.zip';
            }

            if (! str_ends_with($fileName, '.zip')) {
                return $fileName.'.zip';
            }
        }

        return $fileName;
    }
}
