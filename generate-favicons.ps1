# 3Migs Favicon Generator Script
Write-Host "🎨 3Migs Favicon Generator" -ForegroundColor Cyan
Write-Host "=========================" -ForegroundColor Cyan

# Check if ImageMagick is available
$magickAvailable = $false
try {
    $null = Get-Command magick -ErrorAction Stop
    $magickAvailable = $true
    Write-Host "✅ ImageMagick found!" -ForegroundColor Green
} catch {
    Write-Host "❌ ImageMagick not found. Using alternative method." -ForegroundColor Yellow
}

if ($magickAvailable) {
    Write-Host "`n🔄 Generating favicon files with ImageMagick..." -ForegroundColor Blue
    
    # Generate different sizes
    $sizes = @(
        @{size="16x16"; file="favicon-16x16.png"},
        @{size="32x32"; file="favicon-32x32.png"},
        @{size="180x180"; file="apple-touch-icon.png"},
        @{size="192x192"; file="android-chrome-192x192.png"},
        @{size="512x512"; file="android-chrome-512x512.png"}
    )
    
    foreach ($item in $sizes) {
        try {
            magick "3migs-logo.png" -resize $item.size "public\$($item.file)"
            Write-Host "✅ Generated $($item.file)" -ForegroundColor Green
        } catch {
            Write-Host "❌ Failed to generate $($item.file)" -ForegroundColor Red
        }
    }
    
    # Generate favicon.ico
    try {
        magick "3migs-logo.png" -define icon:auto-resize=64,48,32,16 "public\favicon.ico"
        Write-Host "✅ Generated favicon.ico" -ForegroundColor Green
    } catch {
        Write-Host "❌ Failed to generate favicon.ico" -ForegroundColor Red
    }
} else {
    Write-Host "`n📋 Manual Instructions:" -ForegroundColor Yellow
    Write-Host "1. Open favicon-generator.html in your browser" -ForegroundColor White
    Write-Host "2. Download all 5 PNG files" -ForegroundColor White
    Write-Host "3. Place them in the public/ directory" -ForegroundColor White
    Write-Host "4. Generate favicon.ico from 32x32 PNG at https://favicon.io/favicon-converter/" -ForegroundColor White
}

Write-Host "`n📁 Required Files:" -ForegroundColor Cyan
Write-Host "public/favicon.ico" -ForegroundColor White
Write-Host "public/favicon-16x16.png" -ForegroundColor White
Write-Host "public/favicon-32x32.png" -ForegroundColor White
Write-Host "public/apple-touch-icon.png" -ForegroundColor White
Write-Host "public/android-chrome-192x192.png" -ForegroundColor White
Write-Host "public/android-chrome-512x512.png" -ForegroundColor White

Write-Host "`n🎉 Favicon setup complete!" -ForegroundColor Green
Write-Host "Clear your browser cache (Ctrl+F5) to see the new favicon." -ForegroundColor Yellow