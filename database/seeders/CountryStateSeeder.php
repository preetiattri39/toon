<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountryStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load JSON file
        $json = File::get(public_path('country_json/countries.json'));
        $countries = json_decode($json, true);

        foreach ($countries as $country) {
            // Check if the country already exists
            $existingCountry = DB::table('countries')->where('name', $country['name'])->orWhere('country_code', $country['code2'])->first();

            if (!$existingCountry) {
                // Insert country if not exists
                $countryId = DB::table('countries')->insertGetId([
                    'name' => $country['name'],
                    'country_code' => $country['code2'],
                    'ordering' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Use the existing country's ID
                $countryId = $existingCountry->id;
            }

            // Insert states
            if (!empty($country['states'])) {
                foreach ($country['states'] as $state) {
                    // Check if the state already exists for the country
                    $existingState = DB::table('country_states')
                        ->where('country_id', $countryId)
                        ->where('name', $state['name'])
                        ->first();

                    if (!$existingState) {
                        // Insert state if not exists
                        DB::table('country_states')->insert([
                            'country_id' => $countryId,
                            'name' => $state['name'],
                            'state_code' => $state['code'] ?? null,
                            'ordering' => null,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
