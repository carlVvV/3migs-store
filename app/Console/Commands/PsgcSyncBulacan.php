<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\PSGCCity;
use App\Models\PSGCProvince;
use Illuminate\Support\Facades\Log;

class PsgcSyncBulacan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psgc:sync-bulacan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all Bulacan cities and municipalities from psgc.cloud';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Syncing Bulacan cities and municipalities...');
        $this->newLine();

        // Bulacan province code
        $bulacanProvinceCode = '031400000';
        
        // Find Bulacan province
        $province = PSGCProvince::where('code', $bulacanProvinceCode)->first();
        
        if (!$province) {
            $this->error('❌ Bulacan province not found in database!');
            $this->warn('Please run: php artisan psgc:sync (to sync provinces first)');
            return 1;
        }

        $this->info("✅ Found Bulacan: {$province->name} ({$province->code})");
        $this->newLine();

        // Fetch cities
        $this->info('Fetching cities from psgc.cloud...');
        $citiesResponse = Http::timeout(30)->get('https://psgc.cloud/api/cities');
        
        if ($citiesResponse->failed()) {
            $this->error('Failed to fetch cities from psgc.cloud.');
            return 1;
        }

        // Fetch municipalities
        $this->info('Fetching municipalities from psgc.cloud...');
        $municipalitiesResponse = Http::timeout(30)->get('https://psgc.cloud/api/municipalities');
        
        if ($municipalitiesResponse->failed()) {
            $this->error('Failed to fetch municipalities from psgc.cloud.');
            return 1;
        }

        $allLocations = array_merge($citiesResponse->json(), $municipalitiesResponse->json());
        
        // Filter for Bulacan (province_code = 031400000)
        $bulacanCities = array_filter($allLocations, function ($loc) use ($bulacanProvinceCode) {
            $provinceCode = $loc['provinceCode'] ?? $loc['province_code'] ?? null;
            return $provinceCode === $bulacanProvinceCode;
        });

        $total = count($bulacanCities);
        
        if ($total == 0) {
            $this->warn('⚠️  No Bulacan cities found in API response.');
            $this->info('Trying alternative method: filtering by city code pattern...');
            
            // Alternative: filter by code pattern (0314xxxxxx = Bulacan)
            $bulacanCities = array_filter($allLocations, function ($loc) {
                $code = $loc['code'] ?? '';
                return strlen($code) >= 4 && substr($code, 0, 4) === '0314';
            });
            
            $total = count($bulacanCities);
        }

        if ($total == 0) {
            $this->error('❌ No Bulacan cities found. API might be down or data format changed.');
            return 1;
        }

        $this->info("Found {$total} Bulacan cities/municipalities. Syncing to database...");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($bulacanCities as $loc) {
            if (empty($loc['code']) || empty($loc['name'])) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $code = $loc['code'];
            $existing = PSGCCity::where('code', $code)->first();

            // Get data from API
            $regionCode = $loc['regionCode'] ?? $loc['region_code'] ?? $province->region_code ?? '030000000';
            $regionName = $loc['regionName'] ?? $loc['region_name'] ?? $province->region_name ?? 'Region III (Central Luzon)';
            $provinceCode = $loc['provinceCode'] ?? $loc['province_code'] ?? $bulacanProvinceCode;
            $provinceName = $loc['provinceName'] ?? $loc['province_name'] ?? $province->name;

            $data = [
                'code' => $code,
                'name' => $loc['name'],
                'type' => $loc['type'] ?? null,
                'district' => $loc['district'] ?? null,
                'zip_code' => $loc['zip_code'] ?? null,
                'region_code' => $regionCode,
                'region_name' => $regionName,
                'province_code' => $bulacanProvinceCode, // Force Bulacan code
                'province_name' => $provinceName,
            ];

            if ($existing) {
                // Update existing record
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
                // Create new record
                try {
                    PSGCCity::create($data);
                    $created++;
                } catch (\Exception $e) {
                    Log::error("Failed to create Bulacan city: {$loc['name']}", [
                        'code' => $code,
                        'error' => $e->getMessage()
                    ]);
                    $skipped++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Bulacan sync complete!");
        $this->info("   Created: {$created} new cities");
        $this->info("   Updated: {$updated} existing cities");
        if ($skipped > 0) {
            $this->warn("   Skipped: {$skipped} cities");
        }
        $this->newLine();

        // Verify
        $finalCount = PSGCCity::where('province_code', $bulacanProvinceCode)->count();
        $this->info("📊 Total Bulacan cities in database: {$finalCount}");
        
        if ($finalCount < 20) {
            $this->warn("⚠️  Expected ~21 cities for Bulacan. You may need to run the full sync.");
        } else {
            $this->info("✓ Good! Bulacan has {$finalCount} cities.");
        }
        
        $this->newLine();
        $this->warn("💡 Don't forget to clear cache:");
        $this->warn("   php artisan cache:clear");

        return 0;
    }
}
