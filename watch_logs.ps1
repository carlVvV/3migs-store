# Laravel Log Viewer for Windows
# This script monitors Laravel logs in real-time

Write-Host "=== LARAVEL LOG MONITOR ===" -ForegroundColor Green
Write-Host "Monitoring: storage/logs/laravel.log" -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop monitoring" -ForegroundColor Red
Write-Host ""

# Check if log file exists
if (-not (Test-Path "storage/logs/laravel.log")) {
    Write-Host "ERROR: Log file not found at storage/logs/laravel.log" -ForegroundColor Red
    Write-Host "Make sure you're in the Laravel project directory" -ForegroundColor Yellow
    exit 1
}

# Show last 20 lines and then monitor for new content
Write-Host "=== LAST 20 LOG ENTRIES ===" -ForegroundColor Cyan
Get-Content storage/logs/laravel.log -Tail 20

Write-Host ""
Write-Host "=== MONITORING FOR NEW LOGS ===" -ForegroundColor Cyan
Write-Host "Submit a product form to see new logs appear..." -ForegroundColor Yellow
Write-Host ""

# Monitor for new content
Get-Content storage/logs/laravel.log -Wait -Tail 0
