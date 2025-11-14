<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\PSGCCity;
use App\Models\PSGCRegion;
use App\Models\PSGCProvince;
use Illuminate\Support\Facades\Log;

class PsgcFullSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psgc:sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all cities and municipalities from psgc.cloud and update/create them in the local database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching all cities from psgc.cloud...');
        $citiesResponse = Http::get('https://psgc.cloud/api/cities');
        
        $this->info('Fetching all municipalities from psgc.cloud...');
        $municipalitiesResponse = Http::get('https://psgc.cloud/api/municipalities');
        
        if ($citiesResponse->failed() || $municipalitiesResponse->failed()) {
            $this->error('Failed to fetch data from psgc.cloud.');
            return 1;
        }
        
        $locations = array_merge($citiesResponse->json(), $municipalitiesResponse->json());
        $total = count($locations);
        
        if ($total == 0) {
            $this->error('No locations found. API might be down.');
            return 1;
        }
        
        $this->info("Found $total total locations. Starting database sync (this may take a minute)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($locations as $loc) {
            if (empty($loc['code']) || empty($loc['name'])) {
                $skipped++;
                $bar->advance();
                continue;
            }
            
            $code = $loc['code'];
            $existing = PSGCCity::where('code', $code)->first();
            
            // Get region/province codes from API (try multiple field name variations)
            $regionCode = $loc['regionCode'] ?? $loc['region_code'] ?? null;
            $regionName = $loc['regionName'] ?? $loc['region_name'] ?? null;
            $provinceCode = $loc['provinceCode'] ?? $loc['province_code'] ?? null;
            $provinceName = $loc['provinceName'] ?? $loc['province_name'] ?? null;
            
            // Prepare base data
            $data = [
                'name' => $loc['name'],
                'type' => $loc['type'] ?? null,
                'district' => $loc['district'] ?? null,
                'zip_code' => $loc['zip_code'] ?? null,
            ];
            
            if ($existing) {
                // For existing records, update all fields including missing region/province names
                if ($regionCode !== null) {
                    $data['region_code'] = $regionCode;
                }
                if ($regionName !== null) {
                    $data['region_name'] = $regionName;
                } elseif ($existing->region_code && empty($existing->region_name)) {
                    // Look up region name from database if missing
                    $region = PSGCRegion::where('code', $existing->region_code)->first();
                    if ($region) {
                        $data['region_name'] = $region->name;
                    }
                }
                
                if ($provinceCode !== null) {
                    $data['province_code'] = $provinceCode;
                }
                if ($provinceName !== null) {
                    $data['province_name'] = $provinceName;
                } elseif ($existing->province_code && empty($existing->province_name)) {
                    // Look up province name from database if missing
                    $province = PSGCProvince::where('code', $existing->province_code)->first();
                    if ($province) {
                        $data['province_name'] = $province->name;
                    }
                }
                
                // Update only changed fields
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
                // For new records, derive codes if missing
                if ($regionCode === null && strlen($code) >= 2) {
                    // Extract region code from city code (first 2 digits + 0000000)
                    $regionCode = substr($code, 0, 2) . '0000000';
                }
                
                if ($provinceCode === null && strlen($code) >= 4) {
                    // Extract province code from city code
                    // Example: 031411000 (Malolos) -> 031400000 (Bulacan)
                    // Pattern: first 4 digits (0314) + "00" + "000" = 031400000
                    $provinceCode = substr($code, 0, 4) . '00000';
                }
                
                // Skip if we still don't have required fields
                if (empty($regionCode) || empty($provinceCode)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                // Add required fields for new records
                $data['code'] = $code;
                $data['region_code'] = $regionCode;
                $data['province_code'] = $provinceCode;
                $data['region_name'] = $regionName ?? '';
                $data['province_name'] = $provinceName ?? '';
                
                // Check for duplicates by name
                $duplicate = PSGCCity::where('name', $loc['name'])
                    ->where('code', '!=', $code)
                    ->first();
                
                if ($duplicate) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                PSGCCity::create($data);
                $created++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Database sync complete!");
        $this->info("Created: $created new records");
        $this->info("Updated: $updated existing records");
        if ($skipped > 0) {
            $this->warn("Skipped: $skipped records (missing code or name)");
        }
        $this->newLine();
        $this->warn("Now, clear your cache to see the changes:");
        $this->warn("php artisan cache:clear");
        
        return 0;
    }
}
