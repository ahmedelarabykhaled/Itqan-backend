<?php

namespace App\Console\Commands;

use App\Models\QuranImage;
use App\Models\QuranImagePart;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DownloadQuranImages extends Command
{
    /**
     * @var list<int>
     */
    public const WIDTHS = [320, 480, 800, 1024, 1260, 1280, 1920];

    public const TOTAL_PAGES = 604;

    protected $signature = 'quran-images:download
                            {--widths=* : Specific widths to download (defaults to all)}
                            {--pages=604 : Number of pages to download per width}
                            {--force : Re-download and overwrite existing files}
                            {--skip-parts : Only download ayahinfo archives}
                            {--concurrency=10 : Concurrent page downloads per batch}';

    protected $description = 'Download Quran ayahinfo archives and page parts for each width';

    public function handle(): int
    {
        $widths = $this->resolveWidths();
        $pages = max(1, min((int) $this->option('pages'), self::TOTAL_PAGES));
        $force = (bool) $this->option('force');
        $skipParts = (bool) $this->option('skip-parts');
        $concurrency = max(1, (int) $this->option('concurrency'));

        $this->info(sprintf(
            'Downloading %d width(s), %d page(s) each%s.',
            count($widths),
            $pages,
            $skipParts ? ' (skipping parts)' : '',
        ));

        foreach ($widths as $width) {
            $image = $this->downloadAyahInfo($width, $force);

            if ($image === null) {
                $this->error("Failed to download ayahinfo for width {$width}.");

                continue;
            }

            if ($skipParts) {
                continue;
            }

            $this->downloadParts($image, $width, $pages, $force, $concurrency);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function resolveWidths(): array
    {
        $requested = array_values(array_filter(
            array_map('intval', (array) $this->option('widths')),
            fn (int $width): bool => $width > 0,
        ));

        if ($requested === []) {
            return self::WIDTHS;
        }

        $invalid = array_diff($requested, self::WIDTHS);

        if ($invalid !== []) {
            $this->warn('Ignoring unsupported widths: '.implode(', ', $invalid));
        }

        $widths = array_values(array_intersect(self::WIDTHS, $requested));

        return $widths !== [] ? $widths : self::WIDTHS;
    }

    private function downloadAyahInfo(int $width, bool $force): ?QuranImage
    {
        $sourceUrl = "https://files.quran.app/hafs/madani/databases/ayahinfo/ayahinfo_{$width}.zip";
        $storagePath = "quran-images/ayahinfo_{$width}.zip";

        $existing = QuranImage::query()->where('width', $width)->first();

        if ($existing !== null && ! $force && filled($existing->file) && Storage::disk('public')->exists($existing->file)) {
            $this->line("Skipping ayahinfo {$width} (already exists).");

            return $existing;
        }

        $this->line("Downloading ayahinfo for width {$width}...");

        $response = Http::timeout(120)
            ->withHeaders(['User-Agent' => 'ItqanQuranImagesDownloader/1.0'])
            ->get($sourceUrl);

        if (! $response->successful()) {
            $this->error("HTTP {$response->status()} for {$sourceUrl}");

            return null;
        }

        Storage::disk('public')->put($storagePath, $response->body());

        return QuranImage::query()->updateOrCreate(
            ['width' => $width],
            [
                'source_url' => $sourceUrl,
                'file' => $storagePath,
            ],
        );
    }

    private function downloadParts(
        QuranImage $image,
        int $width,
        int $pages,
        bool $force,
        int $concurrency,
    ): void {
        $this->line("Downloading {$pages} parts for width {$width}...");

        $bar = $this->output->createProgressBar($pages);
        $bar->start();

        foreach (array_chunk(range(1, $pages), $concurrency) as $batch) {
            $responses = Http::pool(function (Pool $pool) use ($batch, $width) {
                $requests = [];

                foreach ($batch as $page) {
                    $pageLabel = str_pad((string) $page, 3, '0', STR_PAD_LEFT);
                    $url = "https://files.quran.app/hafs/madani/width_{$width}/page{$pageLabel}.png";

                    $requests[$page] = $pool
                        ->as((string) $page)
                        ->withHeaders(['User-Agent' => 'ItqanQuranImagesDownloader/1.0'])
                        ->timeout(60)
                        ->get($url);
                }

                return $requests;
            });

            foreach ($batch as $page) {
                $this->storePart(
                    $image,
                    $width,
                    $page,
                    $responses[(string) $page] ?? null,
                    $force,
                );

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    private function storePart(
        QuranImage $image,
        int $width,
        int $page,
        mixed $response,
        bool $force,
    ): void {
        $pageLabel = str_pad((string) $page, 3, '0', STR_PAD_LEFT);
        $sourceUrl = "https://files.quran.app/hafs/madani/width_{$width}/page{$pageLabel}.png";
        $storagePath = "quran-image-parts/{$width}/page{$pageLabel}.zip";

        $existing = QuranImagePart::query()
            ->where('quran_image_id', $image->id)
            ->where('source_url', $sourceUrl)
            ->first();

        if ($existing !== null && ! $force && filled($existing->file) && Storage::disk('public')->exists($existing->file)) {
            return;
        }

        if (! $response instanceof Response || ! $response->successful()) {
            $status = $response instanceof Response ? $response->status() : 'no-response';
            $this->newLine();
            $this->error("Failed page {$pageLabel} for width {$width} (HTTP {$status}).");

            return;
        }

        $zipBinary = $this->zipPngContents("page{$pageLabel}.png", $response->body());

        if ($zipBinary === null) {
            $this->newLine();
            $this->error("Failed to create zip for page {$pageLabel} (width {$width}).");

            return;
        }

        Storage::disk('public')->put($storagePath, $zipBinary);

        QuranImagePart::query()->updateOrCreate(
            [
                'quran_image_id' => $image->id,
                'source_url' => $sourceUrl,
            ],
            [
                'file' => $storagePath,
            ],
        );
    }

    private function zipPngContents(string $entryName, string $pngContents): ?string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'quran-page-');

        if ($tempPath === false) {
            return null;
        }

        $zipPath = $tempPath.'.zip';
        @unlink($tempPath);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        $zip->addFromString($entryName, $pngContents);
        $zip->close();

        $binary = file_get_contents($zipPath);
        @unlink($zipPath);

        return $binary === false ? null : $binary;
    }
}
