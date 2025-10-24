<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\PSGCRegion;
use App\Models\PSGCProvince;
use App\Models\PSGCCity;
use App\Models\PSGCBarangay;

class PSGCSeeder extends Seeder
{
    private $baseUrl = 'https://psgc.gitlab.io/api';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting PSGC data seeding...');
        
        // Clear existing data
        PSGCBarangay::truncate();
        PSGCCity::truncate();
        PSGCProvince::truncate();
        PSGCRegion::truncate();
        
        $this->seedRegions();
        $this->seedProvinces();
        $this->seedCities();
        $this->seedNCRCities();
        $this->seedBarangays();
        
        $this->command->info('PSGC data seeding completed!');
    }

    private function seedRegions()
    {
        $this->command->info('Seeding regions...');
        
        $response = Http::get("{$this->baseUrl}/regions");
        
        if ($response->successful()) {
            $regions = $response->json();
            
            foreach ($regions as $region) {
                PSGCRegion::create([
                    'code' => $region['code'],
                    'name' => $region['name'],
                ]);
            }
            
            $this->command->info("Seeded " . count($regions) . " regions");
        } else {
            $this->command->error('Failed to fetch regions from PSGC API');
        }
    }

    private function seedProvinces()
    {
        $this->command->info('Seeding provinces...');
        
        $regions = PSGCRegion::all();
        $totalProvinces = 0;
        
        foreach ($regions as $region) {
            $response = Http::get("{$this->baseUrl}/regions/{$region->code}/provinces");
            
            if ($response->successful()) {
                $provinces = $response->json();
                
                foreach ($provinces as $province) {
                    PSGCProvince::create([
                        'code' => $province['code'],
                        'name' => $province['name'],
                        'region_code' => $region->code,
                        'region_name' => $region->name,
                    ]);
                }
                
                $totalProvinces += count($provinces);
                $this->command->info("Seeded " . count($provinces) . " provinces for {$region->name}");
            } else {
                $this->command->error("Failed to fetch provinces for region {$region->name}");
            }
        }
        
        $this->command->info("Total provinces seeded: {$totalProvinces}");
    }

    private function seedCities()
    {
        $this->command->info('Seeding cities...');
        
        $provinces = PSGCProvince::all();
        $totalCities = 0;
        
        foreach ($provinces as $province) {
            $response = Http::get("{$this->baseUrl}/provinces/{$province->code}/cities");
            
            if ($response->successful()) {
                $cities = $response->json();
                
                foreach ($cities as $city) {
                    PSGCCity::create([
                        'code' => $city['code'],
                        'name' => $city['name'],
                        'type' => $city['type'] ?? null,
                        'district' => $city['district'] ?? null,
                        'zip_code' => $city['zip_code'] ?? null,
                        'region_code' => $province->region_code,
                        'region_name' => $province->region_name,
                        'province_code' => $province->code,
                        'province_name' => $province->name,
                    ]);
                }
                
                $totalCities += count($cities);
                $this->command->info("Seeded " . count($cities) . " cities for {$province->name}");
            } else {
                $this->command->error("Failed to fetch cities for province {$province->name}");
            }
        }
        
        $this->command->info("Total cities seeded: {$totalCities}");
    }

    private function seedNCRCities()
    {
        $this->command->info('Seeding NCR cities...');
        
        $ncrRegion = PSGCRegion::where('code', '130000000')->first();
        if (!$ncrRegion) {
            $this->command->error('NCR region not found');
            return;
        }
        
        $response = Http::get("{$this->baseUrl}/regions/{$ncrRegion->code}/cities");
        
        if ($response->successful()) {
            $cities = $response->json();
            $totalCities = 0;
            
            foreach ($cities as $city) {
                PSGCCity::create([
                    'code' => $city['code'],
                    'name' => $city['name'],
                    'type' => $city['type'] ?? null,
                    'district' => $city['district'] ?? null,
                    'zip_code' => $city['zip_code'] ?? null,
                    'region_code' => $ncrRegion->code,
                    'region_name' => $ncrRegion->name,
                    'province_code' => null, // NCR cities have no province
                    'province_name' => null,
                ]);
                $totalCities++;
            }
            
            $this->command->info("Seeded {$totalCities} NCR cities");
        } else {
            $this->command->error('Failed to fetch NCR cities');
        }
    }

    private function seedBarangays()
    {
        $this->command->info('Seeding barangays...');
        
        $cities = PSGCCity::all();
        $totalBarangays = 0;
        
        foreach ($cities as $city) {
            $response = Http::get("{$this->baseUrl}/cities/{$city->code}/barangays");
            
            if ($response->successful()) {
                $barangays = $response->json();
                
                foreach ($barangays as $barangay) {
                    PSGCBarangay::create([
                        'code' => $barangay['code'],
                        'name' => $barangay['name'],
                        'region_code' => $city->region_code,
                        'region_name' => $city->region_name,
                        'province_code' => $city->province_code,
                        'province_name' => $city->province_name,
                        'city_code' => $city->code,
                        'city_name' => $city->name,
                    ]);
                }
                
                $totalBarangays += count($barangays);
                $this->command->info("Seeded " . count($barangays) . " barangays for {$city->name}");
            } else {
                // Skip cities without barangays (some might not have barangay data)
                $this->command->warn("No barangays found for city {$city->name}");
            }
        }
        
        $this->command->info("Total barangays seeded: {$totalBarangays}");
    }
}