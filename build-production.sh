#!/bin/bash

# Production Build Script for Heroku
# This script ensures Vite assets are built correctly

echo "🚀 Starting production build process..."

# Install dependencies
echo "📦 Installing dependencies..."
npm install

# Build assets
echo "🔨 Building Vite assets..."
npm run build

# Verify build
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Vite manifest created successfully"
    echo "📁 Build directory contents:"
    ls -la public/build/
else
    echo "❌ Vite manifest not found - build may have failed"
    echo "📁 Public directory contents:"
    ls -la public/
    exit 1
fi

echo "🎉 Production build completed successfully!"
