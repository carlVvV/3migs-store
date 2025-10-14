# Heroku Deployment Guide for 3Migs Barong Store

## ✅ Files Created/Updated for Heroku Deployment

### 1. Procfile
```
web: vendor/bin/heroku-php-apache2 public/
```

### 2. app.json
```json
{
  "buildpacks": [
    {
      "url": "heroku/nodejs"
    },
    {
      "url": "heroku/php"
    }
  ]
}
```

### 3. package.json (Updated)
Added `"postinstall": "npm run build"` to automatically build assets during deployment.

## 🚀 Deployment Steps

### Step 1: Set Heroku Buildpacks
```bash
heroku buildpacks:set heroku/nodejs
heroku buildpacks:add heroku/php
```

### Step 2: Set Environment Variables
```bash
# Set your Supabase database credentials
heroku config:set DB_CONNECTION=pgsql
heroku config:set DB_HOST=your-supabase-host.supabase.co
heroku config:set DB_PORT=5432
heroku config:set DB_DATABASE=postgres
heroku config:set DB_USERNAME=postgres
heroku config:set DB_PASSWORD=your-supabase-password
heroku config:set DB_SSLMODE=require

# Set app configuration
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set APP_URL=https://your-app-name.herokuapp.com

# Generate app key
heroku run php artisan key:generate

# Set Google OAuth (update redirect URI)
heroku config:set GOOGLE_CLIENT_ID=your-google-client-id
heroku config:set GOOGLE_CLIENT_SECRET=your-google-client-secret
heroku config:set GOOGLE_REDIRECT_URI=https://your-app-name.herokuapp.com/auth/google/callback
```

### Step 3: Deploy and Run Migrations
```bash
git add .
git commit -m "Add Heroku deployment configuration"
git push heroku main

# Run migrations
heroku run php artisan migrate --force

# Seed database
heroku run php artisan db:seed --force
```

## 🔧 Troubleshooting

### If Vite manifest error persists:
1. Check that buildpacks are set correctly
2. Verify that `postinstall` script runs during deployment
3. Check Heroku logs: `heroku logs --tail`

### If database connection fails:
1. Verify Supabase credentials
2. Check that Supabase allows connections from Heroku
3. Ensure SSL mode is set correctly

### If Google OAuth fails:
1. Update Google Cloud Console with Heroku URL
2. Set correct redirect URI in environment variables

## 📝 Important Notes

- The `postinstall` script will automatically run `npm run build` after `npm install`
- This ensures Vite assets are built during deployment
- Make sure your Supabase database allows connections from Heroku's IP ranges
- Update Google OAuth redirect URIs to include your Heroku domain
