<?php

namespace App\Console\Commands;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanQuranRecitations extends Command
{
    protected $signature = 'quran-recitations:clean
                            {--reciters=* : Specific reciter slugs to clean if deleted from database}
                            {--all-orphans : Also scan existing reciters for orphaned audio files not in database}
                            {--dry-run : Simulate cleanup and report files/directories that would be deleted}';

    protected $description = 'Delete audio files and directories for Quran reciters that were removed from the database';

    public const BASE_DIRECTORY = 'quran-recitation-ayahs';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $dryRun = (bool) $this->option('dry-run');
        $checkAllOrphans = (bool) $this->option('all-orphans');

        if (! $disk->exists(self::BASE_DIRECTORY)) {
            $this->info('No recitations storage directory found.');

            return self::SUCCESS;
        }

        $allDirectories = $disk->directories(self::BASE_DIRECTORY);

        if ($allDirectories === []) {
            $this->info('No reciter directories found in storage.');

            return self::SUCCESS;
        }

        $requestedReciters = array_values(array_filter(
            array_map('strval', (array) $this->option('reciters')),
            fn (string $slug): bool => $slug !== '',
        ));

        if ($requestedReciters !== []) {
            $allDirectories = array_values(array_filter(
                $allDirectories,
                fn (string $dir): bool => in_array(basename($dir), $requestedReciters, true),
            ));

            if ($allDirectories === []) {
                $this->warn('None of the requested reciters were found in storage.');

                return self::SUCCESS;
            }
        }

        $existingSlugs = QuranRecitation::query()
            ->pluck('slug')
            ->flip()
            ->all();

        $deletedRecitersCount = 0;
        $deletedFilesCount = 0;
        $orphanedFilesCount = 0;

        $this->info(sprintf(
            'Scanning %d reciter directory(ies)%s...',
            count($allDirectories),
            $dryRun ? ' [DRY RUN]' : '',
        ));

        foreach ($allDirectories as $dir) {
            $slug = basename($dir);

            if (! isset($existingSlugs[$slug])) {
                $filesInDir = $disk->allFiles($dir);
                $fileCount = count($filesInDir);

                if ($dryRun) {
                    $this->line(sprintf(' [DRY RUN] Would delete reciter folder: %s (%d files)', $dir, $fileCount));
                } else {
                    $disk->deleteDirectory($dir);
                    $this->info(sprintf(' Deleted reciter folder: %s (%d files)', $dir, $fileCount));
                }

                $deletedRecitersCount++;
                $deletedFilesCount += $fileCount;

                continue;
            }

            if ($checkAllOrphans) {
                $recitation = QuranRecitation::query()->where('slug', $slug)->first();

                if ($recitation !== null) {
                    $validPaths = QuranRecitationAyah::query()
                        ->where('quran_recitation_id', $recitation->id)
                        ->get(['file', 'mp3_file'])
                        ->flatMap(fn (QuranRecitationAyah $ayah): array => array_filter([$ayah->file, $ayah->mp3_file]))
                        ->flip()
                        ->all();

                    $storedFiles = $disk->allFiles($dir);

                    foreach ($storedFiles as $storedFile) {
                        if (! isset($validPaths[$storedFile])) {
                            if ($dryRun) {
                                $this->line(sprintf('  [DRY RUN] Would delete orphaned file: %s', $storedFile));
                            } else {
                                $disk->delete($storedFile);
                                $this->info(sprintf('  Deleted orphaned file: %s', $storedFile));
                            }

                            $orphanedFilesCount++;
                        }
                    }
                }
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Deleted Reciter Folders', $deletedRecitersCount],
                ['Deleted Files in Removed Folders', $deletedFilesCount],
                ['Orphaned Files in Existing Folders', $orphanedFilesCount],
                ['Mode', $dryRun ? 'DRY RUN (no changes made)' : 'LIVE (files deleted)'],
            ],
        );

        return self::SUCCESS;
    }
}
