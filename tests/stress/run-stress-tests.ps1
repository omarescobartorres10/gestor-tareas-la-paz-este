# PowerShell Script for Running Stress Tests
# TaskFlow Application Stress Testing Suite

param(
    [string]$TestType = "all",
    [int]$Concurrent = 10,
    [int]$Iterations = 100,
    [switch]$Cleanup,
    [switch]$UseArtillery,
    [string]$BaseUrl = "http://localhost"
)

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "   TaskFlow Stress Testing Suite" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Check if we're in the right directory
if (!(Test-Path "artisan")) {
    Write-Host "Error: Please run this script from the Laravel project root directory" -ForegroundColor Red
    exit 1
}

# Function to run Laravel Artisan stress test
function Run-ArtisanStressTest {
    param(
        [string]$Type,
        [int]$Concurrent,
        [int]$Iterations,
        [switch]$Cleanup
    )
    
    Write-Host "Running Laravel Artisan Stress Tests..." -ForegroundColor Yellow
    Write-Host ""
    
    $cleanupFlag = if ($Cleanup) { "--cleanup" } else { "" }
    
    $command = "php artisan test:stress --type=$Type --concurrent=$Concurrent --iterations=$Iterations $cleanupFlag"
    Write-Host "Executing: $command" -ForegroundColor Gray
    Write-Host ""
    
    Invoke-Expression $command
}

# Function to run Artillery stress test
function Run-ArtilleryStressTest {
    param(
        [string]$BaseUrl
    )
    
    Write-Host "Running Artillery HTTP Stress Tests..." -ForegroundColor Yellow
    Write-Host ""
    
    # Check if Artillery is installed
    $artilleryInstalled = Get-Command artillery -ErrorAction SilentlyContinue
    
    if (!$artilleryInstalled) {
        Write-Host "Artillery is not installed. Installing globally..." -ForegroundColor Yellow
        npm install -g artillery
        
        if ($LASTEXITCODE -ne 0) {
            Write-Host "Failed to install Artillery. Please install it manually: npm install -g artillery" -ForegroundColor Red
            return
        }
    }
    
    # Update target URL in config
    $configPath = "tests/stress/artillery-config.yml"
    if (Test-Path $configPath) {
        Write-Host "Running Artillery with config: $configPath" -ForegroundColor Gray
        Write-Host "Target: $BaseUrl" -ForegroundColor Gray
        Write-Host ""
        
        # Create a temporary config with the correct URL
        $content = Get-Content $configPath -Raw
        $tempContent = $content -replace 'target: "http://localhost"', "target: `"$BaseUrl`""
        $tempConfig = "tests/stress/artillery-temp.yml"
        $tempContent | Out-File -FilePath $tempConfig -Encoding UTF8
        
        # Run Artillery
        artillery run $tempConfig --output tests/stress/report.json
        
        # Generate HTML report if JSON was created
        if (Test-Path "tests/stress/report.json") {
            Write-Host ""
            Write-Host "Generating HTML report..." -ForegroundColor Yellow
            artillery report tests/stress/report.json --output tests/stress/stress-report.html
            Write-Host "Report saved to: tests/stress/stress-report.html" -ForegroundColor Green
        }
        
        # Cleanup temp file
        Remove-Item $tempConfig -ErrorAction SilentlyContinue
    } else {
        Write-Host "Artillery config file not found at: $configPath" -ForegroundColor Red
    }
}

# Function to run quick database stress test
function Run-QuickDatabaseTest {
    Write-Host "Running Quick Database Stress Test..." -ForegroundColor Yellow
    Write-Host ""
    
    php artisan test:stress --type=database --iterations=50 --cleanup
}

# Function to run memory leak detection
function Run-MemoryLeakTest {
    Write-Host "Running Memory Leak Detection..." -ForegroundColor Yellow
    Write-Host ""
    
    php artisan test:stress --type=memory --iterations=200 --cleanup
}

# Function to display menu
function Show-Menu {
    Write-Host ""
    Write-Host "Available Test Options:" -ForegroundColor Cyan
    Write-Host "  1. Full Stress Test (all types)" -ForegroundColor White
    Write-Host "  2. Database Operations Test" -ForegroundColor White
    Write-Host "  3. Cache Operations Test" -ForegroundColor White
    Write-Host "  4. Search Performance Test" -ForegroundColor White
    Write-Host "  5. Concurrent Writes Test" -ForegroundColor White
    Write-Host "  6. Memory Usage Test" -ForegroundColor White
    Write-Host "  7. Artillery HTTP Load Test" -ForegroundColor White
    Write-Host "  8. Quick Database Test (50 iterations)" -ForegroundColor White
    Write-Host "  9. Memory Leak Detection (200 iterations)" -ForegroundColor White
    Write-Host "  0. Exit" -ForegroundColor White
    Write-Host ""
}

# Main execution
if ($UseArtillery) {
    Run-ArtilleryStressTest -BaseUrl $BaseUrl
} elseif ($TestType -eq "menu") {
    $continue = $true
    while ($continue) {
        Show-Menu
        $choice = Read-Host "Select an option (0-9)"
        
        switch ($choice) {
            "1" { Run-ArtisanStressTest -Type "all" -Concurrent $Concurrent -Iterations $Iterations -Cleanup:$Cleanup }
            "2" { Run-ArtisanStressTest -Type "database" -Concurrent $Concurrent -Iterations $Iterations -Cleanup:$Cleanup }
            "3" { Run-ArtisanStressTest -Type "cache" -Concurrent $Concurrent -Iterations $Iterations -Cleanup:$Cleanup }
            "4" { Run-ArtisanStressTest -Type "search" -Concurrent $Concurrent -Iterations $Iterations -Cleanup:$Cleanup }
            "5" { Run-ArtisanStressTest -Type "concurrent_writes" -Concurrent $Concurrent -Iterations $Iterations -Cleanup:$Cleanup }
            "6" { Run-ArtisanStressTest -Type "memory" -Concurrent $Concurrent -Iterations $Iterations -Cleanup:$Cleanup }
            "7" { Run-ArtilleryStressTest -BaseUrl $BaseUrl }
            "8" { Run-QuickDatabaseTest }
            "9" { Run-MemoryLeakTest }
            "0" { $continue = $false }
            default { Write-Host "Invalid option. Please select 0-9." -ForegroundColor Red }
        }
        
        if ($continue -and $choice -ne "0") {
            Write-Host ""
            Read-Host "Press Enter to continue..."
        }
    }
} else {
    Run-ArtisanStressTest -Type $TestType -Concurrent $Concurrent -Iterations $Iterations -Cleanup:$Cleanup
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "   Stress Testing Complete" -ForegroundColor Cyan  
Write-Host "================================================" -ForegroundColor Cyan
