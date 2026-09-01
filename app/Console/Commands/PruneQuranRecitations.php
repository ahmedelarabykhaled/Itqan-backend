<?php

namespace App\Console\Commands;

use App\Models\QuranRecitation;
use App\Support\QuranReciterCatalog;
use Illuminate\Console\Command;

class PruneQuranRecitations extends Command
{
    protected $signature = 'quran-recitations:prune
                            {--list : Show the reciters that will be kept}
                            {--dry-run : Show reciters that would be deleted without deleting them}
                            {--force : Delete extra reciters without confirmation}';

    protected $description = 'Delete Quran reciters that are not in the approved keep list';

    public function handle(): int
    {
        if ((bool) $this->option('list')) {
            $this->renderKeepList();

            return self::SUCCESS;
        }

        $keepSlugs = QuranReciterCatalog::slugs();
        $dryRun = (bool) $this->option('dry-run');

        $extraReciters = QuranRecitation::query()
            ->whereNotIn('slug', $keepSlugs)
            ->orderBy('name_en')
            ->get(['id', 'slug', 'name_en']);

        if ($extraReciters->isEmpty()) {
            $this->info(sprintf(
                'Nothing to prune. All %d reciter(s) in the database are in the keep list.',
                QuranRecitation::query()->count(),
            ));

            return self::SUCCESS;
        }

        $this->warn(sprintf(
            '%s %d reciter(s) not in the keep list:',
            $dryRun ? '[DRY RUN] Would delete' : 'Found',
            $extraReciters->count(),
        ));

        $this->table(
            ['Slug', 'Name'],
            $extraReciters->map(fn (QuranRecitation $reciter): array => [
                $reciter->slug,
                $reciter->name_en,
            ])->all(),
        );

        if ($dryRun) {
            $this->info('No reciters were deleted.');

            return self::SUCCESS;
        }

        if (! (bool) $this->option('force')) {
            $confirmed = $this->confirm(sprintf(
                'This will permanently delete %d reciter(s) and their audio files. Continue?',
                $extraReciters->count(),
            ));

            if (! $confirmed) {
                $this->info('Cancelled.');

                return self::SUCCESS;
            }
        }

        $deletedCount = 0;

        foreach ($extraReciters as $reciter) {
            $reciter->delete();
            $deletedCount++;
            $this->info(sprintf(' Deleted reciter: %s (%s)', $reciter->slug, $reciter->name_en));
        }

        $this->newLine();
        $this->info(sprintf('Deleted %d reciter(s).', $deletedCount));

        return self::SUCCESS;
    }

    private function renderKeepList(): void
    {
        $this->info(sprintf('Keeping %d reciter(s):', count(QuranReciterCatalog::all())));

        $this->table(
            ['#', 'Name', 'Slug', 'Bitrate'],
            collect(QuranReciterCatalog::all())
                ->values()
                ->map(fn (array $reciter, int $index): array => [
                    $index + 1,
                    $reciter['name_en'],
                    $reciter['slug'],
                    $reciter['bitrate'],
                ])
                ->all(),
        );
    }
}
