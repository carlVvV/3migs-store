<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PSGCCity;
use App\Models\PSGCProvince;
use Illuminate\Support\Facades\Cache;

class PsgcDiagnose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psgc:diagnose {province? : Province name to diagnose (e.g., Bulacan)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose PSGC data issues - check province codes, city counts, and cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provinceName = $this->argument('province') ?? 'Bulacan';
        
        $this->info("🔍 Diagnosing PSGC data for: {$provinceName}");
        $this->newLine();

        // 1. Find the province
        $province = PSGCProvince::where('name', 'LIKE', "%{$provinceName}%")->first();
        
        if (!$province) {
            $this->error("❌ Province '{$provinceName}' not found in database!");
            $this->info("Available provinces:");
            PSGCProvince::orderBy('name')->get(['name', 'code'])->each(function ($p) {
                $this->line("  - {$p->name} ({$p->code})");
            });
            return 1;
        }

        $this->info("✅ Found Province:");
        $this->line("   Name: {$province->name}");
        $this->line("   Code: {$province->code}");
        $this->line("   Region: {$province->region_name} ({$province->region_code})");
        $this->newLine();

        // 2. Count cities for this province
        $cities = PSGCCity::where('province_code', $province->code)->get();
        $cityCount = $cities->count();

        $this->info("📊 Cities in Database:");
        $this->line("   Total cities for {$province->name}: {$cityCount}");
        
        if ($cityCount === 0) {
            $this->error("   ⚠️  NO CITIES FOUND! This is the problem.");
        } elseif ($cityCount < 10) {
            $this->warn("   ⚠️  Very few cities ({$cityCount}). Expected 20+ for {$province->name}.");
        } else {
            $this->info("   ✓ Good number of cities");
        }
        $this->newLine();

        // 3. Show sample cities
        if ($cityCount > 0) {
            $this->info("📋 Sample Cities (first 10):");
            $sampleCities = $cities->take(10);
            $headers = ['Code', 'Name', 'Type', 'Zip Code', 'Province Code'];
            $rows = $sampleCities->map(function ($city) {
                return [
                    $city->code,
                    $city->name,
                    $city->type ?? 'N/A',
                    $city->zip_code ?? 'N/A',
                    $city->province_code,
                ];
            })->toArray();
            $this->table($headers, $rows);
            $this->newLine();
        }

        // 4. Check for province code mismatches
        $this->info("🔍 Checking for Province Code Mismatches:");
        $allCities = PSGCCity::where('province_name', 'LIKE', "%{$provinceName}%")->get();
        $mismatched = $allCities->filter(function ($city) use ($province) {
            return $city->province_code !== $province->code;
        });

        if ($mismatched->count() > 0) {
            $this->warn("   ⚠️  Found {$mismatched->count()} cities with mismatched province codes:");
            $mismatched->take(5)->each(function ($city) use ($province) {
                $this->line("      - {$city->name}: has '{$city->province_code}' but should be '{$province->code}'");
            });
        } else {
            $this->info("   ✓ No mismatches found");
        }
        $this->newLine();

        // 5. Check cache
        $cacheKey = "philippine_address_cities_{$province->code}";
        $cached = Cache::get($cacheKey);
        
        $this->info("💾 Cache Status:");
        if ($cached) {
            $cachedCount = is_array($cached) ? count($cached) : 'N/A';
            $this->line("   Cache key: {$cacheKey}");
            $this->line("   Cached cities: {$cachedCount}");
            $this->warn("   ⚠️  Cache exists - clear it to see fresh data:");
            $this->warn("      php artisan cache:clear");
        } else {
            $this->info("   ✓ No cache for this province");
        }
        $this->newLine();

        // 6. Recommendations
        $this->info("💡 Recommendations:");
        if ($cityCount === 0) {
            $this->error("   1. Run: php artisan psgc:sync-all");
            $this->error("   2. Or: php artisan psgc:sync");
            $this->error("   3. Then: php artisan cache:clear");
        } elseif ($cityCount < 10) {
            $this->warn("   1. Run: php artisan psgc:sync-all (to get more cities)");
            $this->warn("   2. Then: php artisan cache:clear");
        } else {
            $this->info("   1. If cities still not showing, clear cache:");
            $this->info("      php artisan cache:clear");
        }

        return 0;
    }
}
