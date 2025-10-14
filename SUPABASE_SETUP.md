# Supabase PostgreSQL Configuration Guide

## Step 1: Create .env file
Create a `.env` file in your project root with the following configuration:

```env
APP_NAME="3Migs Gowns & Barong"
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration for Supabase PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=aws-1-us-east-2.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.cvjhbcxppofdgmyxhtom
DB_PASSWORD=your-actual-password-here
DB_SSLMODE=require

# IMPORTANT: Do NOT use DB_URL with special characters in password
# Use individual DB_* variables instead to avoid URL parsing issues

# Other configurations...
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Google OAuth Configuration
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Cloudinary Configuration (if using)
CLOUDINARY_URL=your-cloudinary-url
```

## Step 2: Get Supabase Credentials
1. Go to [Supabase Dashboard](https://supabase.com/dashboard)
2. Create a new project or select existing project
3. Go to Settings > Database
4. Copy the connection details:
   - Host: `your-project-ref.supabase.co`
   - Database: `postgres`
   - Username: `postgres`
   - Password: Your database password
   - Port: `5432`

## Step 3: Update .env with your Supabase credentials
Replace the placeholder values in your .env file with actual Supabase credentials.

## Step 4: Generate Application Key
Run: `php artisan key:generate`

## Step 5: Run Migrations
Run: `php artisan migrate`

## Step 6: Test Connection
Run: `php artisan tinker` and test:
```php
DB::connection()->getPdo();
```

## Troubleshooting

### Error: "The database configuration URL is malformed"
This error occurs when using `DB_URL` with special characters in the password. **Solution:**
- Remove `DB_URL` from your .env file
- Use individual `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, etc. variables instead
- Ensure passwords with special characters are properly quoted in .env

### Other Common Issues:
- Ensure PostgreSQL extension is enabled in PHP
- Check SSL mode requirements
- Verify Supabase project is active
- Check firewall settings if connection fails
- Make sure your Supabase project is not paused
