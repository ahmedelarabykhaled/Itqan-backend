<?php

namespace App\Console\Commands;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use App\Support\QuranAyahCatalog;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DownloadQuranRecitations extends Command
{
    protected $signature = 'quran-recitations:download
                            {--reciters=* : Specific reciter slugs to download (defaults to all from everyayah)}
                            {--force : Re-download and overwrite existing files}
                            {--concurrency=10 : Concurrent ayah downloads per batch}
                            {--surah= : Limit to a single surah number}
                            {--limit-ayahs= : Limit total ayah downloads per reciter (for testing)}';

    protected $description = 'Download Quran recitation ayah audio from everyayah.com and store as zip files';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $concurrency = max(1, (int) $this->option('concurrency'));
        $surahFilter = filled($this->option('surah')) ? (int) $this->option('surah') : null;
        $limitAyahs = filled($this->option('limit-ayahs')) ? (int) $this->option('limit-ayahs') : null;

        $reciters = $this->resolveReciters();

        if ($reciters === []) {
            $this->error('No reciters found to download.');

            return self::FAILURE;
        }

        $pairs = QuranAyahCatalog::pairs($surahFilter, $limitAyahs);

        $this->info(sprintf(
            'Downloading %d reciter(s), %d ayah file(s) each.',
            count($reciters),
            count($pairs),
        ));

        foreach ($reciters as $reciter) {
            $recitation = $this->upsertRecitation($reciter);

            if ($recitation === null) {
                continue;
            }

            $this->downloadAyahs($recitation, $pairs, $force, $concurrency);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    /**
     * @return list<array{slug: string, name: string, bitrate: ?string}>
     */
    private function resolveReciters(): array
    {
        $requested = array_values(array_filter(
            array_map('strval', (array) $this->option('reciters')),
            fn (string $slug): bool => $slug !== '',
        ));

        if ($requested !== []) {
            return array_map(fn (string $slug): array => [
                'slug' => $slug,
                'name' => $this->humanizeSlug($slug),
                'bitrate' => $this->extractBitrate($slug),
            ], $requested);
        }

        return $this->fetchRecitersFromEveryAyah();
    }

    /**
     * @return list<array{slug: string, name: string, bitrate: ?string}>
     */
    private function fetchRecitersFromEveryAyah(): array
    {
        $this->line('Fetching reciter list from everyayah.com...');

        $response = Http::timeout(60)
            ->withHeaders(['User-Agent' => 'ItqanQuranRecitationsDownloader/1.0'])
            ->get('https://everyayah.com/recitations_ayat.html');

        if (! $response->successful()) {
            $this->error('Failed to fetch reciter list (HTTP '.$response->status().').');

            return [];
        }

        $reciters = [];
        $pattern = '#<strong>([^<]+)</strong>\s*<a href="https://everyayah\.com/data/([^"]+)/"\s*target="_blank">\s*\(GO\)#';

        preg_match_all($pattern, $response->body(), $matches, PREG_SET_ORDER);

        if ($matches === []) {
            $this->warn('Could not parse reciter links from everyayah.com.');

            return [];
        }

        foreach ($matches as $match) {
            $slug = $match[2];
            $reciters[] = [
                'slug' => $slug,
                'name' => trim(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5)),
                'bitrate' => $this->extractBitrate($slug),
            ];
        }

        usort($reciters, fn (array $a, array $b): int => strcmp($a['slug'], $b['slug']));

        return $reciters;
    }

    /**
     * @param  array{slug: string, name: string, bitrate: ?string}  $reciter
     */
    private function upsertRecitation(array $reciter): ?QuranRecitation
    {
        $sourceUrl = 'https://everyayah.com/data/'.$reciter['slug'];

        return QuranRecitation::query()->updateOrCreate(
            ['slug' => $reciter['slug']],
            [
                'name_en' => $reciter['name'],
                'bitrate' => $reciter['bitrate'],
                'source_url' => $sourceUrl,
            ],
        );
    }

    /**
     * @param  list<array{surah: int, ayah: int}>  $pairs
     */
    private function downloadAyahs(
        QuranRecitation $recitation,
        array $pairs,
        bool $force,
        int $concurrency,
    ): void {
        $this->line("Downloading ayahs for {$recitation->slug}...");

        $bar = $this->output->createProgressBar(count($pairs));
        $bar->start();

        foreach (array_chunk($pairs, $concurrency) as $batch) {
            $pending = [];

            foreach ($batch as $pair) {
                $label = QuranAyahCatalog::fileLabel($pair['surah'], $pair['ayah']);
                $sourceUrl = rtrim((string) $recitation->source_url, '/')."/{$label}.mp3";
                $storagePath = "quran-recitation-ayahs/{$recitation->slug}/{$label}.zip";

                $existing = QuranRecitationAyah::query()
                    ->where('quran_recitation_id', $recitation->id)
                    ->where('surah', $pair['surah'])
                    ->where('ayah', $pair['ayah'])
                    ->first();

                if ($existing !== null && ! $force && filled($existing->file) && Storage::disk('public')->exists($existing->file)) {
                    $bar->advance();

                    continue;
                }

                $pending[] = [
                    'pair' => $pair,
                    'label' => $label,
                    'source_url' => $sourceUrl,
                    'storage_path' => $storagePath,
                ];
            }

            if ($pending === []) {
                continue;
            }

            $responses = Http::pool(function (Pool $pool) use ($pending) {
                $requests = [];

                foreach ($pending as $item) {
                    $key = $item['label'];
                    $requests[$key] = $pool
                        ->as($key)
                        ->withHeaders(['User-Agent' => 'ItqanQuranRecitationsDownloader/1.0'])
                        ->timeout(60)
                        ->get($item['source_url']);
                }

                return $requests;
            });

            foreach ($pending as $item) {
                $this->storeAyah(
                    $recitation,
                    $item['pair'],
                    $item['label'],
                    $item['source_url'],
                    $item['storage_path'],
                    $responses[$item['label']] ?? null,
                );

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * @param  array{surah: int, ayah: int}  $pair
     */
    private function storeAyah(
        QuranRecitation $recitation,
        array $pair,
        string $label,
        string $sourceUrl,
        string $storagePath,
        mixed $response,
    ): void {
        if (! $response instanceof Response || ! $response->successful()) {
            return;
        }

        $entryName = "{$label}.mp3";
        $zipBinary = $this->zipMp3Contents($entryName, $response->body());

        if ($zipBinary === null) {
            $this->newLine();
            $this->error("Failed to create zip for {$label} ({$recitation->slug}).");

            return;
        }

        Storage::disk('public')->put($storagePath, $zipBinary);

        $mp3StoragePath = "quran-recitation-ayahs/{$recitation->slug}/{$label}.mp3";
        Storage::disk('public')->put($mp3StoragePath, $response->body());

        QuranRecitationAyah::query()->updateOrCreate(
            [
                'quran_recitation_id' => $recitation->id,
                'surah' => $pair['surah'],
                'ayah' => $pair['ayah'],
            ],
            [
                'source_url' => $sourceUrl,
                'file' => $storagePath,
                'mp3_file' => $mp3StoragePath,
            ],
        );
    }

    private function zipMp3Contents(string $entryName, string $mp3Contents): ?string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'quran-ayah-');

        if ($tempPath === false) {
            return null;
        }

        $zipPath = $tempPath.'.zip';
        @unlink($tempPath);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        $zip->addFromString($entryName, $mp3Contents);
        $zip->close();

        $binary = file_get_contents($zipPath);
        @unlink($zipPath);

        return $binary === false ? null : $binary;
    }

    private function humanizeSlug(string $slug): string
    {
        return str_replace('_', ' ', $slug);
    }

    private function extractBitrate(string $slug): ?string
    {
        if (preg_match('/(\d+kbps|\d+Kbps)/i', $slug, $matches) === 1) {
            return strtolower($matches[1]);
        }

        return null;
    }
}
