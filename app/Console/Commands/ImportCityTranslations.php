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
            ->where('wikiDataId', 'like', 'Q%')
            ->orderBy('id', 'desc')
            ->chunk(500, function ($cities) {

                $groups = $cities->chunk(50);

                $responses = [];

                foreach ($groups as $group) {

                    $ids = $group->pluck('wikiDataId')
                        ->map(fn ($id) => basename($id))
                        ->implode('|');

                    try {
                        $response = Http::withHeaders([
                            'User-Agent' => 'ItqanCitiesImporter/1.0',
                        ])
                            ->retry(3, 2000, function (\Exception $exception, $request) {
                                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                            })
                            ->timeout(60)
                            ->get('https://www.wikidata.org/w/api.php', [
                                'action' => 'wbgetentities',
                                'ids' => $ids,
                                'props' => 'labels',
                                'languages' => 'en|ar|fa|ur|ps|ckb|sd|ug',
                                'format' => 'json',
                            ]);
                        
                        $responses[] = $response;
                        $this->info('Batch fetched: '.$group->count());
                        $this->info(json_encode($group->pluck('wikiDataId')));
                    } catch (\Exception $e) {
                        $this->error('Failed to fetch batch: ' . $e->getMessage());
                    }

                    // Sleep for 1 second between requests to respect Wikidata rate limits
                    sleep(1);
                }

                $rows = [];

                foreach ($responses as $index => $response) {

                    if (! $response->ok()) {
                        $this->error("HTTP Error: " . $response->status());
                        continue;
                    }

                    $data = $response->json();

                    if (isset($data['error'])) {
                        $this->error("API Error: " . json_encode($data['error']));
                        continue;
                    }

                    if (! isset($data['entities'])) {
                        continue;
                    }

                    foreach ($data['entities'] as $qid => $entity) {

                        $matchingCities = $cities->where('wikiDataId', $qid);
                        if ($matchingCities->isEmpty()) {
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

                        foreach ($matchingCities as $city) {
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
