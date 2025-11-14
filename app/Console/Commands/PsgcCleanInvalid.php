<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PSGCCity;
use Illuminate\Support\Facades\DB;

class PsgcCleanInvalid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psgc:clean-invalid {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove invalid city/municipality entries (empty names, "no city", etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        } else {
            $this->info('🧹 Cleaning invalid city/municipality entries...');
            $this->newLine();
        }

        // Find invalid entries - check for various invalid patterns
        $invalidCities = PSGCCity::where(function ($query) {
            $query->whereNull('name')
                  ->orWhere('name', '')
                  ->orWhere('name', 'LIKE', '%no city%')
                  ->orWhere('name', 'LIKE', '%no municipality%')
                  ->orWhere('name', 'LIKE', '%No City%')
                  ->orWhere('name', 'LIKE', '%No Municipality%')
                  ->orWhere('name', 'LIKE', '%NO CITY%')
                  ->orWhere('name', 'LIKE', '%NO MUNICIPALITY%')
                  ->orWhere('name', 'LIKE', '%n/a%')
                  ->orWhere('name', 'LIKE', '%N/A%')
                  ->orWhere('name', 'LIKE', '%null%')
                  ->orWhere('name', 'LIKE', '%NULL%')
                  ->orWhere('name', 'LIKE', '%undefined%')
                  ->orWhere('name', 'LIKE', '%UNDEFINED%')
                  ->orWhere('name', 'LIKE', '%test%')
                  ->orWhere('name', 'LIKE', '%TEST%')
                  ->orWhere(DB::raw('TRIM(COALESCE(name, \'\'))'), '');
        })->get();
        
        // Also check for entries with missing required fields
        $missingFields = PSGCCity::where(function ($query) {
            $query->whereNull('province_code')
                  ->orWhere('province_code', '')
                  ->orWhereNull('region_code')
                  ->orWhere('region_code', '');
        })->get();
        
        // Combine both sets
        $allInvalid = $invalidCities->merge($missingFields)->unique('id');

        $count = $allInvalid->count();

        if ($count === 0) {
            $this->info('✅ No invalid entries found!');
            return 0;
        }

        $this->info("Found {$count} invalid city/municipality entries:");
        $this->newLine();

        // Show what will be deleted
        $headers = ['ID', 'Code', 'Name', 'Type', 'Province', 'Issue'];
        $rows = $allInvalid->map(function ($city) use ($invalidCities, $missingFields) {
            $issue = [];
            if ($invalidCities->contains('id', $city->id)) {
                $issue[] = 'Invalid name';
            }
            if ($missingFields->contains('id', $city->id)) {
                $issue[] = 'Missing required fields';
            }
            return [
                $city->id,
                $city->code ?? 'N/A',
                $city->name ?? '(empty)',
                $city->type ?? 'N/A',
                $city->province_name ?? 'N/A',
                implode(', ', $issue),
            ];
        })->toArray();

        $this->table($headers, $rows);
        $this->newLine();

        if ($dryRun) {
            $this->warn("Would delete {$count} entries. Run without --dry-run to actually delete.");
            return 0;
        }

        // Confirm deletion
        if (!$this->confirm("Are you sure you want to delete {$count} invalid entries?")) {
            $this->info('Cancelled.');
            return 0;
        }

        // Delete invalid entries
        $deleted = 0;
        foreach ($allInvalid as $city) {
            try {
                $city->delete();
                $deleted++;
            } catch (\Exception $e) {
                $this->error("Failed to delete city ID {$city->id}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("✅ Successfully deleted {$deleted} invalid entries!");
        
        // Show remaining count
        $remaining = PSGCCity::count();
        $this->info("📊 Remaining cities/municipalities in database: {$remaining}");

        return 0;
    }
}
