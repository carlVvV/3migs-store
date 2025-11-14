<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PSGCCity;
use App\Models\PSGCProvince;
use App\Models\PSGCRegion;

class PsgcVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psgc:verify {--detailed : Show detailed information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify PSGC data in the database - check counts, zip codes, and data integrity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verifying PSGC Data in Database...');
        $this->newLine();

        // 1. Count totals
        $totalCities = PSGCCity::count();
        $totalProvinces = PSGCProvince::count();
        $totalRegions = PSGCRegion::count();

        $this->info("📊 Total Records:");
        $this->line("   Regions: {$totalRegions}");
        $this->line("   Provinces: {$totalProvinces}");
        $this->line("   Cities/Municipalities: {$totalCities}");
        $this->newLine();

        // 2. Zip code statistics
        $citiesWithZip = PSGCCity::whereNotNull('zip_code')->where('zip_code', '!=', '')->count();
        $citiesWithoutZip = PSGCCity::whereNull('zip_code')->orWhere('zip_code', '')->count();
        $zipPercentage = $totalCities > 0 ? round(($citiesWithZip / $totalCities) * 100, 2) : 0;

        $this->info("📮 Zip Code Coverage:");
        $this->line("   Cities with zip codes: {$citiesWithZip} ({$zipPercentage}%)");
        $this->line("   Cities without zip codes: {$citiesWithoutZip}");
        $this->newLine();

        // 3. Required fields check
        $missingRegionCode = PSGCCity::whereNull('region_code')->orWhere('region_code', '')->count();
        $missingProvinceCode = PSGCCity::whereNull('province_code')->orWhere('province_code', '')->count();
        $missingRegionName = PSGCCity::whereNull('region_name')->orWhere('region_name', '')->count();
        $missingProvinceName = PSGCCity::whereNull('province_name')->orWhere('province_name', '')->count();

        $this->info("✅ Data Integrity Check:");
        if ($missingRegionCode > 0 || $missingProvinceCode > 0 || $missingRegionName > 0 || $missingProvinceName > 0) {
            $this->error("   ⚠️  Found records with missing required fields:");
            if ($missingRegionCode > 0) $this->error("      - Missing region_code: {$missingRegionCode}");
            if ($missingProvinceCode > 0) $this->error("      - Missing province_code: {$missingProvinceCode}");
            if ($missingRegionName > 0) $this->error("      - Missing region_name: {$missingRegionName}");
            if ($missingProvinceName > 0) $this->error("      - Missing province_name: {$missingProvinceName}");
        } else {
            $this->info("   ✓ All required fields are present");
        }
        $this->newLine();

        // 4. Recent updates
        $recentUpdates = PSGCCity::where('updated_at', '>=', now()->subDays(7))->count();
        $lastUpdate = PSGCCity::orderBy('updated_at', 'desc')->first();

        $this->info("🕒 Update Status:");
        $this->line("   Records updated in last 7 days: {$recentUpdates}");
        if ($lastUpdate) {
            $this->line("   Last update: {$lastUpdate->updated_at->format('Y-m-d H:i:s')}");
            $this->line("   Last updated city: {$lastUpdate->name} ({$lastUpdate->code})");
        }
        $this->newLine();

        // 5. Sample data (if detailed flag)
        if ($this->option('detailed')) {
            $this->info("📋 Sample Cities (with zip codes):");
            $sampleCities = PSGCCity::whereNotNull('zip_code')
                ->where('zip_code', '!=', '')
                ->limit(10)
                ->get();

            $headers = ['Code', 'Name', 'Type', 'Zip Code', 'Province', 'Region'];
            $rows = $sampleCities->map(function ($city) {
                return [
                    $city->code,
                    $city->name,
                    $city->type ?? 'N/A',
                    $city->zip_code ?? 'N/A',
                    $city->province_name ?? 'N/A',
                    $city->region_name ?? 'N/A',
                ];
            })->toArray();

            $this->table($headers, $rows);
            $this->newLine();

            $this->info("📋 Sample Cities (without zip codes):");
            $sampleNoZip = PSGCCity::whereNull('zip_code')
                ->orWhere('zip_code', '')
                ->limit(5)
                ->get();

            if ($sampleNoZip->count() > 0) {
                $rows = $sampleNoZip->map(function ($city) {
                    return [
                        $city->code,
                        $city->name,
                        $city->type ?? 'N/A',
                        'MISSING',
                        $city->province_name ?? 'N/A',
                        $city->region_name ?? 'N/A',
                    ];
                })->toArray();
                $this->table($headers, $rows);
            } else {
                $this->info("   ✓ All cities have zip codes!");
            }
            $this->newLine();
        }

        // 6. Quick test - check specific cities
        $this->info("🧪 Quick Test - Checking specific cities:");
        $testCities = ['Manila', 'Quezon City', 'Cebu City', 'Davao City'];
        foreach ($testCities as $cityName) {
            $city = PSGCCity::where('name', 'LIKE', "%{$cityName}%")->first();
            if ($city) {
                $zipStatus = $city->zip_code ? "✓ Zip: {$city->zip_code}" : "✗ No zip";
                $this->line("   {$city->name}: {$zipStatus}, Province: {$city->province_name}");
            } else {
                $this->warn("   {$cityName}: Not found");
            }
        }
        $this->newLine();

        // 7. Summary
        $this->info("📈 Summary:");
        if ($zipPercentage >= 80) {
            $this->info("   ✓ Good zip code coverage ({$zipPercentage}%)");
        } elseif ($zipPercentage >= 50) {
            $this->warn("   ⚠️  Moderate zip code coverage ({$zipPercentage}%)");
        } else {
            $this->error("   ✗ Low zip code coverage ({$zipPercentage}%)");
        }

        if ($missingRegionCode > 0 || $missingProvinceCode > 0) {
            $this->error("   ✗ Data integrity issues found - run psgc:sync-all to fix");
        } else {
            $this->info("   ✓ Data integrity is good");
        }

        $this->newLine();
        $this->info("💡 Tip: Use --detailed flag for more information");
        $this->info("💡 Tip: Run 'php artisan psgc:sync-all' to update data");

        return 0;
    }
}
