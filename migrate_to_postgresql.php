<?php

/**
 * Database Migration Helper Script
 * Run this script to help migrate from SQLite to PostgreSQL
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== PostgreSQL Migration Helper ===\n\n";

// Test database connection
try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful!\n";
    echo "Database: " . DB::connection()->getDatabaseName() . "\n";
    echo "Driver: " . DB::connection()->getDriverName() . "\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your .env configuration.\n";
    exit(1);
}

// Check if migrations table exists
if (Schema::hasTable('migrations')) {
    echo "✅ Migrations table exists\n";
    
    // Show migration status
    $migrations = DB::table('migrations')->get();
    echo "Migrations run: " . $migrations->count() . "\n";
    
    if ($migrations->count() > 0) {
        echo "Recent migrations:\n";
        foreach ($migrations->take(5) as $migration) {
            echo "  - " . $migration->migration . "\n";
        }
    }
} else {
    echo "⚠️  Migrations table not found. Run: php artisan migrate:install\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Run: php artisan migrate\n";
echo "2. Run: php artisan db:seed (if you have seeders)\n";
echo "3. Test your application\n\n";

echo "=== PostgreSQL Specific Notes ===\n";
echo "- PostgreSQL uses different date functions than SQLite\n";
echo "- Use TO_CHAR() instead of strftime()\n";
echo "- Use EXTRACT() for date parts\n";
echo "- JSON columns work differently (use JSONB for better performance)\n\n";

echo "Migration helper completed!\n";
