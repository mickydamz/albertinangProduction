<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CountryStateSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Fetching all countries + states from CountriesNow API…');

        $response = Http::timeout(60)
            ->retry(3, 2000)
            ->get('https://countriesnow.space/api/v0.1/countries/states');

        if (!$response->successful()) {
            $this->command->error('CountriesNow API failed: HTTP ' . $response->status());
            return;
        }

        $payload = $response->json();

        if (empty($payload['data']) || !is_array($payload['data'])) {
            $this->command->error('Unexpected response structure from CountriesNow.');
            return;
        }

        Cache::forget('reg:countries:withstates');
        foreach (Country::pluck('id') as $countryId) Cache::forget('geo:states:' . $countryId);

        // Wipe existing data so re-seeding is idempotent
        City::query()->delete();
        Country::query()->delete();
        Cache::forget('geo:countries');

        $items = collect($payload['data'])
            ->sortBy(fn ($c) => $c['name'] === 'Nigeria' ? '' : strtolower($c['name']));

        $bar = $this->command->getOutput()->createProgressBar($items->count());
        $bar->start();

        $seen = []; // guard against duplicate country names in source data

        foreach ($items as $item) {
            $name = trim($item['name'] ?? '');
            if (!$name || isset($seen[$name])) {
                $bar->advance();
                continue;
            }
            $seen[$name] = true;

            $iso = strtoupper(substr($item['iso3'] ?? $item['iso2'] ?? '', 0, 3)) ?: null;

            $country = Country::create([
                'name'     => $name,
                'iso_code' => $iso,
            ]);

            $states  = $item['states'] ?? [];
            $inserts = [];
            $seenStates = [];

            foreach ($states as $state) {
                $stateName = trim($state['name'] ?? '');
                if ($stateName && !isset($seenStates[$stateName])) {
                    $seenStates[$stateName] = true;
                    $inserts[] = [
                        'country_id' => $country->id,
                        'name'       => $stateName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if ($inserts) {
                City::insert($inserts);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        $this->command->info(sprintf(
            'Done. %d countries, %d states/provinces seeded.',
            Country::count(),
            City::count()
        ));
    }
}
