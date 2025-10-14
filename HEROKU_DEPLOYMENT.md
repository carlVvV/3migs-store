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

### Step 1: Set Heroku Buildpacks (IMPORTANT: Order matters!)
```bash
# Set Node.js first (for building assets)
heroku buildpacks:set heroku/nodejs

# Add PHP second (for running the app)
heroku buildpacks:add heroku/php
```

### Step 1.5: Verify Buildpack Order
```bash
heroku buildpacks
# Should show:
# 1. heroku/nodejs
# 2. heroku/php
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

1. **Check buildpack order** (CRITICAL):
   ```bash
   heroku buildpacks
   # Must show Node.js first, then PHP
   ```

2. **Check build process**:
   ```bash
   heroku logs --tail --dyno web
   # Look for "Building Vite assets" or npm build messages
   ```

3. **Verify files exist on Heroku**:
   ```bash
   heroku run ls -la public/build/
   heroku run cat public/build/manifest.json
   ```

4. **Force rebuild**:
   ```bash
   heroku run npm run build
   ```

5. **Check environment**:
   ```bash
   heroku run env | grep NODE
   heroku run env | grep APP_ENV
   ```

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
