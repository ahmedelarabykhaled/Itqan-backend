<?php

namespace App\Console\Commands;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UnzipQuranRecitations extends Command
{
    protected $signature = 'quran-recitations:unzip
                            {--reciters=* : Specific reciter slugs to process (defaults to all)}
                            {--force : Re-extract and overwrite existing MP3 files}
                            {--chunk=200 : Number of ayahs to process per progress batch}';

    protected $description = 'Extract MP3 files from existing Quran recitation ZIP archives and store their paths';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $chunk = max(1, (int) $this->option('chunk'));

        $query = QuranRecitationAyah::query()
            ->whereNotNull('file')
            ->where('file', '!=', '');

        $requested = array_values(array_filter(
            array_map('strval', (array) $this->option('reciters')),
            fn (string $slug): bool => $slug !== '',
        ));

        if ($requested !== []) {
            $recitationIds = QuranRecitation::query()
                ->whereIn('slug', $requested)
                ->pluck('id');

            if ($recitationIds->isEmpty()) {
                $this->error('No matching reciters found.');

                return self::FAILURE;
            }

            $query->whereIn('quran_recitation_id', $recitationIds);
        }

        if (! $force) {
            $query->where(function ($builder): void {
                $builder->whereNull('mp3_file')
                    ->orWhere('mp3_file', '');
            });
        }

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('Nothing to unzip.');

            return self::SUCCESS;
        }

        $this->info("Extracting MP3 files for {$total} ayah archive(s)...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $extracted = 0;
        $skipped = 0;
        $failed = 0;

        $query->orderBy('id')->chunkById($chunk, function ($ayahs) use ($force, $bar, &$extracted, &$skipped, &$failed): void {
            foreach ($ayahs as $ayah) {
                $result = $this->extractAyah($ayah, $force);

                match ($result) {
                    'extracted' => $extracted++,
                    'skipped' => $skipped++,
                    default => $failed++,
                };

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Done. Extracted: {$extracted}, skipped: {$skipped}, failed: {$failed}.");

        return $failed > 0 && $extracted === 0 ? self::FAILURE : self::SUCCESS;
    }

    private function extractAyah(QuranRecitationAyah $ayah, bool $force): string
    {
        $zipRelativePath = (string) $ayah->file;
        $mp3RelativePath = $this->mp3PathFromZip($zipRelativePath);

        if (! $force && filled($ayah->mp3_file) && Storage::disk('public')->exists($ayah->mp3_file)) {
            return 'skipped';
        }

        if (! Storage::disk('public')->exists($zipRelativePath)) {
            $this->newLine();
            $this->error("Missing zip: {$zipRelativePath}");

            return 'failed';
        }

        $absoluteZipPath = Storage::disk('public')->path($zipRelativePath);
        $zip = new ZipArchive;

        if ($zip->open($absoluteZipPath) !== true) {
            $this->newLine();
            $this->error("Could not open zip: {$zipRelativePath}");

            return 'failed';
        }

        $entryName = $this->findMp3Entry($zip);

        if ($entryName === null) {
            $zip->close();
            $this->newLine();
            $this->error("No MP3 entry found in zip: {$zipRelativePath}");

            return 'failed';
        }

        $mp3Contents = $zip->getFromName($entryName);
        $zip->close();

        if ($mp3Contents === false) {
            $this->newLine();
            $this->error("Could not read MP3 entry from zip: {$zipRelativePath}");

            return 'failed';
        }

        Storage::disk('public')->put($mp3RelativePath, $mp3Contents);

        $ayah->update([
            'mp3_file' => $mp3RelativePath,
        ]);

        return 'extracted';
    }

    private function mp3PathFromZip(string $zipRelativePath): string
    {
        if (str_ends_with(strtolower($zipRelativePath), '.zip')) {
            return substr($zipRelativePath, 0, -4).'.mp3';
        }

        return $zipRelativePath.'.mp3';
    }

    private function findMp3Entry(ZipArchive $zip): ?string
    {
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);

            if ($name === false) {
                continue;
            }

            if (str_ends_with(strtolower($name), '.mp3')) {
                return $name;
            }
        }

        return null;
    }
}
