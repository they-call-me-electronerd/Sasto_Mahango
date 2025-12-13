# Color Theme Converter: Orange to Green
# This script converts all orange color codes to green equivalents across all CSS files

Write-Host "================================" -ForegroundColor Cyan
Write-Host "  Color Theme Converter" -ForegroundColor Cyan
Write-Host "  Orange → Green Theme" -ForegroundColor Cyan
Write-Host "================================`n" -ForegroundColor Cyan

# Color mapping: Orange → Green (preserving shade/brightness)
$colorMap = @{
    # Hex colors
    '#f97316' = '#22c55e'   # Primary orange → Medium green
    '#ea580c' = '#16a34a'   # Dark orange → Dark green
    '#fb923c' = '#4ade80'   # Light orange → Light green
    '#c2410c' = '#15803d'   # Darker orange → Darker green
    '#9a3412' = '#166534'   # Very dark orange → Very dark green
    '#fed7aa' = '#bbf7d0'   # Very light orange → Very light green
    '#ffedd5' = '#dcfce7'   # Pale orange → Pale green
    '#fff7ed' = '#f0fdf4'   # Lightest orange bg → Lightest green bg
    '#fef7f4' = '#f0fdf4'   # Light orange bg → Light green bg
    '#fefefe' = '#fefefe'   # Keep white
    '#ffffff' = '#ffffff'   # Keep white
    '#f59e0b' = '#84cc16'   # Amber/yellow → Lime green
    
    # RGB values (used in rgba() functions)
    '249, 115, 22' = '34, 197, 94'    # Primary orange RGB
    '234, 88, 12' = '22, 163, 74'     # Dark orange RGB
    '251, 146, 60' = '74, 222, 128'   # Light orange RGB
    '194, 65, 12' = '21, 128, 61'     # Darker orange RGB
    '154, 52, 18' = '22, 101, 52'     # Very dark orange RGB
    '245, 158, 11' = '132, 204, 22'   # Amber RGB
}

# Get all CSS files
$cssPath = "c:\xampp\htdocs\MulyaSuchi\assets\css"
$cssFiles = Get-ChildItem -Path $cssPath -Filter "*.css" -Recurse

Write-Host "Found $($cssFiles.Count) CSS files to process`n" -ForegroundColor Yellow

$totalReplacements = 0
$processedFiles = 0

foreach ($file in $cssFiles) {
    $relativePath = $file.FullName.Replace($cssPath, "").TrimStart('\')
    Write-Host "Processing: $relativePath" -ForegroundColor Green
    
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content
    $fileReplacements = 0
    
    foreach ($oldColor in $colorMap.Keys) {
        $newColor = $colorMap[$oldColor]
        $matches = ([regex]::Matches($content, [regex]::Escape($oldColor), [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)).Count
        
        if ($matches -gt 0) {
            $content = $content -replace [regex]::Escape($oldColor), $newColor
            $fileReplacements += $matches
            Write-Host "  ├─ $oldColor → $newColor ($matches occurrences)" -ForegroundColor Gray
        }
    }
    
    # Only write if changes were made
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
        $totalReplacements += $fileReplacements
        $processedFiles++
        Write-Host "  └─ Total replacements in file: $fileReplacements" -ForegroundColor Cyan
    } else {
        Write-Host "  └─ No changes needed" -ForegroundColor DarkGray
    }
    
    Write-Host ""
}

Write-Host "================================" -ForegroundColor Cyan
Write-Host "  Conversion Complete!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan
Write-Host "Files processed: $processedFiles / $($cssFiles.Count)" -ForegroundColor Yellow
Write-Host "Total color replacements: $totalReplacements" -ForegroundColor Yellow
Write-Host "`nYour website theme has been successfully converted to green!" -ForegroundColor Green
