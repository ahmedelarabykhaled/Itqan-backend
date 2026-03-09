<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportCityTranslations extends Command
{
    protected $signature = 'cities:import-translations';

    protected $description = 'Import Arabic and English city names from Wikidata';

    public function handle()
    {
        DB::table('cities')
            ->whereNotNull('wikiDataId')
            ->orderBy('id')
            ->chunk(500, function ($cities) {

                $groups = $cities->chunk(50);

                $responses = Http::pool(function ($pool) use ($groups) {

                    $requests = [];

                    foreach ($groups as $group) {

                        $ids = $group->pluck('wikiDataId')
                            ->map(fn ($id) => basename($id))
                            ->implode('|');

                        $requests[] = $pool->withHeaders([
                            'User-Agent' => 'ItqanCitiesImporter/1.0',
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
                        continue;
                    }

                    $data = $response->json();

                    if (! isset($data['entities'])) {
                        continue;
                    }

                    foreach ($data['entities'] as $qid => $entity) {

                        $city = $cities->firstWhere('wikiDataId', $qid);
                        if (! $city) {
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
                                'city_id' => $city->id,
                                'language_code' => 'en',
                                'name' => $nameEn,
                            ];
                        }

                        if ($nameAr) {
                            $rows[] = [
                                'city_id' => $city->id,
                                'language_code' => 'ar',
                                'name' => $nameAr,
                            ];
                        }
                    }
                }

                if ($rows) {

                    DB::table('city_translations')->upsert(
                        $rows,
                        ['city_id', 'language_code'],
                        ['name']
                    );
                }
            });
        $this->info('City translations imported successfully');
    }
}
