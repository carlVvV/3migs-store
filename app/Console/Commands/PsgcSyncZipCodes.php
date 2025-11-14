<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\PSGCCity;
use Illuminate\Support\Facades\Log;

class PsgcSyncZipCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psgc:sync-zips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all zip codes from psgc.cloud and update the local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch data from psgc.cloud...');
        
        try {
            // Fetch all cities and municipalities
            $citiesResponse = Http::get('https://psgc.cloud/api/cities');
            $municipalitiesResponse = Http::get('https://psgc.cloud/api/municipalities');
            
            if ($citiesResponse->failed() || $municipalitiesResponse->failed()) {
                $this->error('Failed to fetch data from psgc.cloud.');
                return 1;
            }
            
            $allLocations = array_merge($citiesResponse->json(), $municipalitiesResponse->json());
            $total = count($allLocations);
            $updated = 0;
            $skipped = 0;
            
            $this->info("Fetched $total total locations. Starting database sync...");
            
            // Create a progress bar
            $bar = $this->output->createProgressBar($total);
            $bar->start();
            
            foreach ($allLocations as $location) {
                if (empty($location['code']) || empty($location['zip_code'])) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                // Find the local city/municipality by its PSGC code
                $localCity = PSGCCity::where('code', $location['code'])->first();
                
                if ($localCity) {
                    // Update the zip code if it's null or different
                    if ($localCity->zip_code != $location['zip_code']) {
                        $localCity->zip_code = $location['zip_code'];
                        $localCity->save();
                        $updated++;
                    }
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            
            $this->info("\nSync complete!");
            $this->info("Updated: $updated");
            $this->info("Skipped (no zip code): $skipped");
            
        } catch (\Exception $e) {
            Log::error('PSGC Sync Failed: ' . $e->getMessage());
            $this->error("\nAn error occurred: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
