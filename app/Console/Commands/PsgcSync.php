<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\PSGCRegion;
use App\Models\PSGCProvince;
use App\Models\PSGCCity;
use App\Models\PSGCBarangay;
use Illuminate\Support\Facades\Log;

class PsgcSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psgc:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync regions, provinces, cities, and municipalities from psgc.cloud API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting PSGC data sync from psgc.cloud...');
        
        try {
            // Step 1: Sync Regions
            $this->info("\n[1/5] Syncing Regions...");
            $this->syncRegions();
            
            // Step 2: Sync Provinces
            $this->info("\n[2/5] Syncing Provinces...");
            $this->syncProvinces();
            
            // Step 3: Sync Cities
            $this->info("\n[3/5] Syncing Cities...");
            $this->syncCities();
            
            // Step 4: Sync Municipalities
            $this->info("\n[4/5] Syncing Municipalities...");
            $this->syncMunicipalities();
            
            // Step 5: Sync Barangays
            $this->info("\n[5/5] Syncing Barangays...");
            $this->syncBarangays();
            
            $this->info("\n✅ PSGC sync complete!");
            
        } catch (\Exception $e) {
            Log::error('PSGC Sync Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error("\n❌ An error occurred: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Sync regions from psgc.cloud
     */
    private function syncRegions()
    {
        $response = Http::get('https://psgc.cloud/api/regions');
        
        if ($response->failed()) {
            $this->error('Failed to fetch regions from psgc.cloud.');
            return;
        }
        
        $regions = $response->json();
        $total = count($regions);
        $created = 0;
        $updated = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($regions as $region) {
            if (empty($region['code']) || empty($region['name'])) {
                $bar->advance();
                continue;
            }
            
            $existing = PSGCRegion::where('code', $region['code'])->first();
            
            if ($existing) {
                if ($existing->name != $region['name']) {
                    $existing->name = $region['name'];
                    $existing->save();
                    $updated++;
                }
            } else {
                PSGCRegion::create([
                    'code' => $region['code'],
                    'name' => $region['name'],
                ]);
                $created++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\n  ✓ Created: {$created}, Updated: {$updated}, Total: {$total}");
    }
    
    /**
     * Sync provinces from psgc.cloud
     */
    private function syncProvinces()
    {
        $response = Http::get('https://psgc.cloud/api/provinces');
        
        if ($response->failed()) {
            $this->error('Failed to fetch provinces from psgc.cloud.');
            return;
        }
        
        $provinces = $response->json();
        $total = count($provinces);
        $created = 0;
        $updated = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($provinces as $province) {
            if (empty($province['code']) || empty($province['name'])) {
                $bar->advance();
                continue;
            }
            
            $existing = PSGCProvince::where('code', $province['code'])->first();
            
            $regionCode = $province['regionCode'] ?? $province['region_code'] ?? null;
            $regionName = $province['regionName'] ?? $province['region_name'] ?? null;
            
            // Only update region fields if they're provided (not null)
            $data = [
                'code' => $province['code'],
                'name' => $province['name'],
            ];
            
            // Only add region fields if they're not null
            if ($regionCode !== null) {
                $data['region_code'] = $regionCode;
            }
            if ($regionName !== null) {
                $data['region_name'] = $regionName;
            }
            
            if ($existing) {
                $needsUpdate = false;
                foreach ($data as $key => $value) {
                    if ($existing->$key != $value) {
                        $existing->$key = $value;
                        $needsUpdate = true;
                    }
                }
                if ($needsUpdate) {
                    $existing->save();
                    $updated++;
                }
            } else {
                // For new records, region_code is required, so skip if null
                if ($regionCode === null) {
                    $this->warn("  ⚠ Skipping province {$province['name']} (code: {$province['code']}) - missing region_code");
                    $bar->advance();
                    continue;
                }
                PSGCProvince::create($data);
                $created++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\n  ✓ Created: {$created}, Updated: {$updated}, Total: {$total}");
    }
    
    /**
     * Sync cities from psgc.cloud
     */
    private function syncCities()
    {
        $response = Http::get('https://psgc.cloud/api/cities');
        
        if ($response->failed()) {
            $this->error('Failed to fetch cities from psgc.cloud.');
            return;
        }
        
        $cities = $response->json();
        $total = count($cities);
        $created = 0;
        $updated = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($cities as $city) {
            if (empty($city['code']) || empty($city['name'])) {
                $bar->advance();
                continue;
            }
            
            $code = $city['code'];
            $existing = PSGCCity::where('code', $code)->first();
            
            $regionCode = $city['regionCode'] ?? $city['region_code'] ?? null;
            $regionName = $city['regionName'] ?? $city['region_name'] ?? null;
            $provinceCode = $city['provinceCode'] ?? $city['province_code'] ?? null;
            $provinceName = $city['provinceName'] ?? $city['province_name'] ?? null;
            
            $data = [
                'code' => $code,
                'name' => $city['name'],
                'type' => $city['type'] ?? 'City',
                'district' => $city['district'] ?? $city['districtCode'] ?? null,
                'zip_code' => $city['zip_code'] ?? $city['zipCode'] ?? null,
            ];
            
            // Only add region/province fields if they're not null
            if ($regionCode !== null) {
                $data['region_code'] = $regionCode;
            }
            if ($regionName !== null) {
                $data['region_name'] = $regionName;
            }
            if ($provinceCode !== null) {
                $data['province_code'] = $provinceCode;
            }
            if ($provinceName !== null) {
                $data['province_name'] = $provinceName;
            }
            
            if ($existing) {
                $needsUpdate = false;
                foreach ($data as $key => $value) {
                    if ($existing->$key != $value) {
                        $existing->$key = $value;
                        $needsUpdate = true;
                    }
                }
                if ($needsUpdate) {
                    $existing->save();
                    $updated++;
                }
            } else {
                // For new records, region_code and province_code are required
                // Try to derive from code if API doesn't provide them
                if ($regionCode === null && strlen($code) >= 2) {
                    // Extract region code from city code (first 2 digits + 0000000)
                    $regionCode = substr($code, 0, 2) . '0000000';
                    $data['region_code'] = $regionCode;
                }
                
                if ($provinceCode === null && strlen($code) >= 6) {
                    // Extract province code from city code (first 6 digits + 0000)
                    $provinceCode = substr($code, 0, 6) . '0000';
                    $data['province_code'] = $provinceCode;
                }
                
                // Check if this is a duplicate (same name exists with different code)
                $duplicate = PSGCCity::where('name', $city['name'])
                    ->where('code', '!=', $code)
                    ->first();
                
                if ($duplicate) {
                    // Skip duplicate entries - we already have this city
                    $bar->advance();
                    continue;
                }
                
                // Still need region_code and province_code to create
                if (empty($data['region_code']) || empty($data['province_code'])) {
                    $this->warn("  ⚠ Skipping city {$city['name']} (code: {$code}) - missing region_code or province_code");
                    $bar->advance();
                    continue;
                }
                
                PSGCCity::create($data);
                $created++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\n  ✓ Created: {$created}, Updated: {$updated}, Total: {$total}");
    }
    
    /**
     * Sync municipalities from psgc.cloud
     */
    private function syncMunicipalities()
    {
        $response = Http::get('https://psgc.cloud/api/municipalities');
        
        if ($response->failed()) {
            $this->error('Failed to fetch municipalities from psgc.cloud.');
            return;
        }
        
        $municipalities = $response->json();
        $total = count($municipalities);
        $created = 0;
        $updated = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($municipalities as $municipality) {
            if (empty($municipality['code']) || empty($municipality['name'])) {
                $bar->advance();
                continue;
            }
            
            $code = $municipality['code'];
            $existing = PSGCCity::where('code', $code)->first();
            
            $regionCode = $municipality['regionCode'] ?? $municipality['region_code'] ?? null;
            $regionName = $municipality['regionName'] ?? $municipality['region_name'] ?? null;
            $provinceCode = $municipality['provinceCode'] ?? $municipality['province_code'] ?? null;
            $provinceName = $municipality['provinceName'] ?? $municipality['province_name'] ?? null;
            
            $data = [
                'code' => $code,
                'name' => $municipality['name'],
                'type' => $municipality['type'] ?? 'Municipality',
                'district' => $municipality['district'] ?? $municipality['districtCode'] ?? null,
                'zip_code' => $municipality['zip_code'] ?? $municipality['zipCode'] ?? null,
            ];
            
            // Only add region/province fields if they're not null
            if ($regionCode !== null) {
                $data['region_code'] = $regionCode;
            }
            if ($regionName !== null) {
                $data['region_name'] = $regionName;
            }
            if ($provinceCode !== null) {
                $data['province_code'] = $provinceCode;
            }
            if ($provinceName !== null) {
                $data['province_name'] = $provinceName;
            }
            
            if ($existing) {
                $needsUpdate = false;
                foreach ($data as $key => $value) {
                    if ($existing->$key != $value) {
                        $existing->$key = $value;
                        $needsUpdate = true;
                    }
                }
                if ($needsUpdate) {
                    $existing->save();
                    $updated++;
                }
            } else {
                $code = $municipality['code'];
                
                // For new records, region_code and province_code are required
                // Try to derive from code if API doesn't provide them
                if ($regionCode === null && strlen($code) >= 2) {
                    // Extract region code from municipality code (first 2 digits + 0000000)
                    $regionCode = substr($code, 0, 2) . '0000000';
                    $data['region_code'] = $regionCode;
                }
                
                if ($provinceCode === null && strlen($code) >= 6) {
                    // Extract province code from municipality code (first 6 digits + 0000)
                    $provinceCode = substr($code, 0, 6) . '0000';
                    $data['province_code'] = $provinceCode;
                }
                
                // Check if this is a duplicate (same name exists with different code)
                $duplicate = PSGCCity::where('name', $municipality['name'])
                    ->where('code', '!=', $code)
                    ->first();
                
                if ($duplicate) {
                    // Skip duplicate entries - we already have this municipality
                    $bar->advance();
                    continue;
                }
                
                // Still need region_code and province_code to create
                if (empty($data['region_code']) || empty($data['province_code'])) {
                    $this->warn("  ⚠ Skipping municipality {$municipality['name']} (code: {$code}) - missing region_code or province_code");
                    $bar->advance();
                    continue;
                }
                
                PSGCCity::create($data);
                $created++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\n  ✓ Created: {$created}, Updated: {$updated}, Total: {$total}");
    }
    
    /**
     * Sync barangays from psgc.cloud
     * Fetches barangays by city since the /api/barangays endpoint may not exist
     */
    private function syncBarangays()
    {
        // First, try to fetch all barangays at once (if endpoint exists)
        $response = Http::timeout(30)->get('https://psgc.cloud/api/barangays');
        
        if ($response->successful()) {
            $this->info('  Using /api/barangays endpoint...');
            $barangays = $response->json();
            $this->syncBarangaysFromArray($barangays);
            return;
        }
        
        // If that fails, fetch barangays by city
        $this->info('  /api/barangays endpoint not available. Fetching by city...');
        $this->info('  This will take longer but is more reliable.');
        
        $cities = PSGCCity::orderBy('code')->get();
        $totalCities = $cities->count();
        $totalBarangays = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        
        $bar = $this->output->createProgressBar($totalCities);
        $bar->start();
        
        foreach ($cities as $city) {
            try {
                // Fetch barangays for this city
                $response = Http::timeout(10)->get("https://psgc.cloud/api/cities/{$city->code}/barangays");
                
                if ($response->successful()) {
                    $barangays = $response->json();
                    
                    foreach ($barangays as $barangay) {
                        if (empty($barangay['code']) || empty($barangay['name'])) {
                            continue;
                        }
                        
                        $existing = PSGCBarangay::where('code', $barangay['code'])->first();
                        
                        $regionCode = $barangay['regionCode'] ?? $barangay['region_code'] ?? $city->region_code ?? null;
                        $regionName = $barangay['regionName'] ?? $barangay['region_name'] ?? $city->region_name ?? null;
                        $provinceCode = $barangay['provinceCode'] ?? $barangay['province_code'] ?? $city->province_code ?? null;
                        $provinceName = $barangay['provinceName'] ?? $barangay['province_name'] ?? $city->province_name ?? null;
                        $cityCode = $barangay['cityCode'] ?? $barangay['city_code'] ?? $city->code ?? null;
                        $cityName = $barangay['cityName'] ?? $barangay['city_name'] ?? $city->name ?? null;
                        
                        $data = [
                            'code' => $barangay['code'],
                            'name' => $barangay['name'],
                        ];
                        
                        if ($regionCode !== null) {
                            $data['region_code'] = $regionCode;
                        }
                        if ($regionName !== null) {
                            $data['region_name'] = $regionName;
                        }
                        if ($provinceCode !== null) {
                            $data['province_code'] = $provinceCode;
                        }
                        if ($provinceName !== null) {
                            $data['province_name'] = $provinceName;
                        }
                        if ($cityCode !== null) {
                            $data['city_code'] = $cityCode;
                        }
                        if ($cityName !== null) {
                            $data['city_name'] = $cityName;
                        }
                        
                        if ($existing) {
                            $needsUpdate = false;
                            foreach ($data as $key => $value) {
                                if ($existing->$key != $value) {
                                    $existing->$key = $value;
                                    $needsUpdate = true;
                                }
                            }
                            if ($needsUpdate) {
                                $existing->save();
                                $updated++;
                            }
                        } else {
                            if ($cityCode === null) {
                                $skipped++;
                                continue;
                            }
                            PSGCBarangay::create($data);
                            $created++;
                        }
                        
                        $totalBarangays++;
                    }
                } else {
                    // Log failed city but continue
                    Log::warning("Failed to fetch barangays for city: {$city->name} ({$city->code})");
                }
                
                // Small delay to avoid rate limiting
                usleep(100000); // 0.1 second delay
                
            } catch (\Exception $e) {
                Log::error("Error fetching barangays for city {$city->name}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\n  ✓ Created: {$created}, Updated: {$updated}, Skipped: {$skipped}, Total: {$totalBarangays}");
    }
    
    /**
     * Helper method to sync barangays from an array (used when /api/barangays works)
     */
    private function syncBarangaysFromArray($barangays)
    {
        $total = count($barangays);
        $created = 0;
        $updated = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($barangays as $barangay) {
            if (empty($barangay['code']) || empty($barangay['name'])) {
                $bar->advance();
                continue;
            }
            
            $existing = PSGCBarangay::where('code', $barangay['code'])->first();
            
            $regionCode = $barangay['regionCode'] ?? $barangay['region_code'] ?? null;
            $regionName = $barangay['regionName'] ?? $barangay['region_name'] ?? null;
            $provinceCode = $barangay['provinceCode'] ?? $barangay['province_code'] ?? null;
            $provinceName = $barangay['provinceName'] ?? $barangay['province_name'] ?? null;
            $cityCode = $barangay['cityCode'] ?? $barangay['city_code'] ?? null;
            $cityName = $barangay['cityName'] ?? $barangay['city_name'] ?? null;
            
            $data = [
                'code' => $barangay['code'],
                'name' => $barangay['name'],
            ];
            
            if ($regionCode !== null) {
                $data['region_code'] = $regionCode;
            }
            if ($regionName !== null) {
                $data['region_name'] = $regionName;
            }
            if ($provinceCode !== null) {
                $data['province_code'] = $provinceCode;
            }
            if ($provinceName !== null) {
                $data['province_name'] = $provinceName;
            }
            if ($cityCode !== null) {
                $data['city_code'] = $cityCode;
            }
            if ($cityName !== null) {
                $data['city_name'] = $cityName;
            }
            
            if ($existing) {
                $needsUpdate = false;
                foreach ($data as $key => $value) {
                    if ($existing->$key != $value) {
                        $existing->$key = $value;
                        $needsUpdate = true;
                    }
                }
                if ($needsUpdate) {
                    $existing->save();
                    $updated++;
                }
            } else {
                if ($cityCode === null) {
                    $bar->advance();
                    continue;
                }
                PSGCBarangay::create($data);
                $created++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\n  ✓ Created: {$created}, Updated: {$updated}, Total: {$total}");
    }
}
