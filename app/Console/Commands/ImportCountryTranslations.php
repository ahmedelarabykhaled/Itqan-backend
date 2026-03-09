<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportCountryTranslations extends Command
{
    protected $signature = 'countries:import-translations';

    protected $description = 'Import Arabic and English country names from Wikidata';

    public function handle()
    {
        $this->info('Starting country translations import...');

        DB::table('countries')
            ->whereNotNull('wikiDataId')
            ->orderBy('id')
            ->chunk(200, function ($countries) {

                $groups = $countries->chunk(50);

                $responses = Http::pool(function ($pool) use ($groups) {

                    $requests = [];

                    foreach ($groups as $group) {

                        $ids = $group->pluck('wikiDataId')
                            ->map(fn ($id) => basename($id))
                            ->implode('|');

                        $requests[] = $pool->withHeaders([
                            'User-Agent' => 'ItqanCountriesImporter/1.0',
                        ])
                            ->timeout(30)
                            ->get('https://www.wikidata.org/w/api.php', [
                                'action' => 'wbgetentities',
                                'ids' => $ids,
                                'props' => 'labels',
                                'languages' => 'en|ar|fa|ur|ps|ckb|sd|ug',
                                'format' => 'json',
                            ]);
                    }

                    return $requests;
                });

                $rows = [];

                foreach ($responses as $response) {

                    if (! $response->ok()) {
                        $this->error('Request failed');

                        continue;
                    }

                    $data = $response->json();

                    if (! isset($data['entities'])) {
                        continue;
                    }

                    foreach ($data['entities'] as $qid => $entity) {

                        $country = $countries->firstWhere('wikiDataId', $qid);

                        if (! $country) {
                            continue;
                        }

                        $labels = $entity['labels'] ?? [];

                        $nameEn = $labels['en']['value'] ?? null;

                        $nameAr =
                            $labels['ar']['value'] ??
                            $labels['fa']['value'] ??
                            $labels['ur']['value'] ??
                            $labels['ps']['value'] ??
                            $labels['ckb']['value'] ??
                            $labels['sd']['value'] ??
                            $labels['ug']['value'] ??
                            null;

                        if ($nameEn) {
                            $rows[] = [
                                'country_id' => $country->id,
                                'language_code' => 'en',
                                'name' => $nameEn,
                            ];
                        }

                        if ($nameAr) {
                            $rows[] = [
                                'country_id' => $country->id,
                                'language_code' => 'ar',
                                'name' => $nameAr,
                            ];
                        }
                    }
                }

                if (! empty($rows)) {

                    DB::table('country_translations')->upsert(
                        $rows,
                        ['country_id', 'language_code'],
                        ['name']
                    );
                }

                $this->info('Batch imported: '.$countries->count());
            });

        $this->info('Country translations import finished.');
    }
}
