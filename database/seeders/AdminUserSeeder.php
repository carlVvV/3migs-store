<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database with a default admin user.
     */
    public function run(): void
    {
        // Create or update the default admin account
        User::updateOrCreate(
            ['email' => 'admin@3migs.com'],
            [
                'name' => 'Site Administrator',
                'password' => 'Admin@12345', // casted & hashed by User model
                'role' => 'admin',
                'email_notifications' => true,
                'marketing_emails' => false,
            ]
        );
    }
}


