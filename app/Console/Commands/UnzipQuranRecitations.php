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
                            {--delete-zip : Delete the ZIP archive after a successful MP3 extract}
                            {--chunk=200 : Number of ayahs to process per progress batch}';

    protected $description = 'Extract MP3 files from Quran recitation ZIP archives, optionally deleting the ZIP afterward';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $deleteZip = (bool) $this->option('delete-zip');
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

        if (! $force && ! $deleteZip) {
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

        $this->info(sprintf(
            'Processing %d ayah archive(s)%s...',
            $total,
            $deleteZip ? ' (ZIP will be deleted after extract)' : '',
        ));

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $extracted = 0;
        $skipped = 0;
        $deleted = 0;
        $failed = 0;

        $query->orderBy('id')->chunkById($chunk, function ($ayahs) use ($force, $deleteZip, $bar, &$extracted, &$skipped, &$deleted, &$failed): void {
            foreach ($ayahs as $ayah) {
                $result = $this->processAyah($ayah, $force, $deleteZip);

                if ($result['extracted']) {
                    $extracted++;
                } elseif ($result['skipped']) {
                    $skipped++;
                }

                if ($result['deleted']) {
                    $deleted++;
                }

                if ($result['failed']) {
                    $failed++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Done. Extracted: {$extracted}, skipped: {$skipped}, zip deleted: {$deleted}, failed: {$failed}.");

        return $failed > 0 && $extracted === 0 && $deleted === 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{extracted: bool, skipped: bool, deleted: bool, failed: bool}
     */
    private function processAyah(QuranRecitationAyah $ayah, bool $force, bool $deleteZip): array
    {
        $result = [
            'extracted' => false,
            'skipped' => false,
            'deleted' => false,
            'failed' => false,
        ];

        $zipRelativePath = (string) $ayah->file;
        $mp3RelativePath = filled($ayah->mp3_file)
            ? (string) $ayah->mp3_file
            : $this->mp3PathFromZip($zipRelativePath);

        $hasMp3 = filled($ayah->mp3_file) && Storage::disk('public')->exists($ayah->mp3_file);

        if ($hasMp3 && ! $force) {
            $result['skipped'] = true;
        } else {
            $extracted = $this->extractMp3($ayah, $zipRelativePath, $mp3RelativePath);

            if (! $extracted) {
                $result['failed'] = true;

                return $result;
            }

            $result['extracted'] = true;
            $ayah->refresh();
        }

        if ($deleteZip) {
            $result['deleted'] = $this->deleteZip($ayah, $zipRelativePath);
        }

        return $result;
    }

    private function extractMp3(QuranRecitationAyah $ayah, string $zipRelativePath, string $mp3RelativePath): bool
    {
        if (! Storage::disk('public')->exists($zipRelativePath)) {
            $this->newLine();
            $this->error("Missing zip: {$zipRelativePath}");

            return false;
        }

        $absoluteZipPath = Storage::disk('public')->path($zipRelativePath);
        $zip = new ZipArchive;

        if ($zip->open($absoluteZipPath) !== true) {
            $this->newLine();
            $this->error("Could not open zip: {$zipRelativePath}");

            return false;
        }

        $entryName = $this->findMp3Entry($zip);

        if ($entryName === null) {
            $zip->close();
            $this->newLine();
            $this->error("No MP3 entry found in zip: {$zipRelativePath}");

            return false;
        }

        $mp3Contents = $zip->getFromName($entryName);
        $zip->close();

        if ($mp3Contents === false) {
            $this->newLine();
            $this->error("Could not read MP3 entry from zip: {$zipRelativePath}");

            return false;
        }

        Storage::disk('public')->put($mp3RelativePath, $mp3Contents);

        $ayah->update([
            'mp3_file' => $mp3RelativePath,
        ]);

        return true;
    }

    private function deleteZip(QuranRecitationAyah $ayah, string $zipRelativePath): bool
    {
        if (! filled($ayah->mp3_file) || ! Storage::disk('public')->exists($ayah->mp3_file)) {
            $this->newLine();
            $this->error("Refusing to delete zip without an existing MP3: {$zipRelativePath}");

            return false;
        }

        if (Storage::disk('public')->exists($zipRelativePath)) {
            Storage::disk('public')->delete($zipRelativePath);
        }

        $ayah->update([
            'file' => null,
        ]);

        return true;
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
