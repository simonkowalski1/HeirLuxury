# Reorganize imports folder to match ImportBrands.php naming convention
# Run this from: C:\Users\simon\Dev\Laravel\HeirLuxury

$importsPath = "storage\app\public\imports"

# Navigate to imports directory
Set-Location $importsPath

Write-Host "Starting reorganization..." -ForegroundColor Green

# Function to merge folders
function Merge-ProductFolder {
    param($source, $destination)

    if (Test-Path $source) {
        if (Test-Path $destination) {
            Write-Host "Merging: $source -> $destination" -ForegroundColor Yellow
            # Move all subdirectories from source to destination
            Get-ChildItem $source | ForEach-Object {
                $destPath = Join-Path $destination $_.Name
                if (Test-Path $destPath) {
                    Write-Host "  WARNING: $($_.Name) already exists in destination, skipping" -ForegroundColor Red
                } else {
                    Move-Item $_.FullName $destPath -Force
                }
            }
            # Remove empty source folder
            Remove-Item $source -Recurse -Force
        } else {
            Write-Host "Renaming: $source -> $destination" -ForegroundColor Cyan
            Rename-Item $source $destination
        }
    }
}

# LV (Louis Vuitton) - Fix LV Belt to be men's
Write-Host "`nProcessing Louis Vuitton..." -ForegroundColor Magenta
Merge-ProductFolder "LV Belt" "lv-belts-men"
Merge-ProductFolder "LV glasses" "lv-glasses-women"
Merge-ProductFolder "LV jewelry" "lv-jewelry-women"
Merge-ProductFolder "LV Men clothes" "lv-clothes-men"
Merge-ProductFolder "LV Men shoes" "lv-shoes-men"

# Remove old format if exists (lv-belts-women is WRONG, should be men)
if (Test-Path "lv-belts-women") {
    Write-Host "ERROR: lv-belts-women exists - checking if it should be men's..." -ForegroundColor Red
    Write-Host "  Please manually review and merge into lv-belts-men if needed" -ForegroundColor Yellow
}

# Celine
Write-Host "`nProcessing Celine..." -ForegroundColor Magenta
Merge-ProductFolder "Celine Bag" "celine-bags-women"
Merge-ProductFolder "Celine belt" "celine-belts-women"
Merge-ProductFolder "CELINE glasses" "celine-glasses-women"
Merge-ProductFolder "Celine jewelry" "celine-jewelry-women"
Merge-ProductFolder "CELINE Men clothes" "celine-clothes-men"
Merge-ProductFolder "CELINE Men shoes" "celine-shoes-men"
Merge-ProductFolder "Celine Women clothess" "celine-clothes-women"
Merge-ProductFolder "CELINE women shoes" "celine-shoes-women"

# Dior
Write-Host "`nProcessing Dior..." -ForegroundColor Magenta
Merge-ProductFolder "Dior Bag" "dior-bags-women"
Merge-ProductFolder "DIOR belt" "dior-belts-women"
Merge-ProductFolder "Dior glasses" "dior-glasses-women"
Merge-ProductFolder "DiOr jewelry" "dior-jewelry-women"
Merge-ProductFolder "DIOR Men clothes" "dior-clothes-men"
Merge-ProductFolder "DIOR Men shoes" "dior-shoes-men"
Merge-ProductFolder "DIOR Women clothes" "dior-clothes-women"
Merge-ProductFolder "DIOR Women shoes" "dior-shoes-women"

# Givenchy
Write-Host "`nProcessing Givenchy..." -ForegroundColor Magenta
Merge-ProductFolder "Givenchy Bag" "givenchy-bags-women"
Merge-ProductFolder "GIVENCHY belt" "givenchy-belts-women"
Merge-ProductFolder "Givenchy glasses" "givenchy-glasses-women"
Merge-ProductFolder "Givenchy jewelry" "givenchy-jewelry-women"
Merge-ProductFolder "GIVENCHY Men clothes" "givenchy-clothes-men"
Merge-ProductFolder "Givenchy Men shoes" "givenchy-shoes-men"
Merge-ProductFolder "Givenchy Women clothes" "givenchy-clothes-women"
Merge-ProductFolder "Givenchy Women shoe" "givenchy-shoes-women"

# McQueen
Write-Host "`nProcessing McQueen..." -ForegroundColor Magenta
Merge-ProductFolder "McQueen Men shoes" "mcqueen-shoes-men"
Merge-ProductFolder "McQueen Women shoes" "mcqueen-shoes-women"

# Moncler
Write-Host "`nProcessing Moncler..." -ForegroundColor Magenta
Merge-ProductFolder "Moncler Men clothes" "moncler-clothes-men"
Merge-ProductFolder "Moncler Men Shoes" "moncler-shoes-men"
Merge-ProductFolder "Moncler Women clothes" "moncler-clothes-women"

# Nike
Write-Host "`nProcessing Nike..." -ForegroundColor Magenta
if (Test-Path "Nike") {
    Rename-Item "Nike" "nike-shoes-men"
}

# Off-White
Write-Host "`nProcessing Off-White..." -ForegroundColor Magenta
Merge-ProductFolder "OFF Men clothes OFF" "offwhite-clothes-men"
Merge-ProductFolder "OFF Men shoes OFF" "offwhite-shoes-men"
Merge-ProductFolder "OFF Women clothess" "offwhite-clothes-women"
Merge-ProductFolder "OFF Women shoes OFF" "offwhite-shoes-women"
Merge-ProductFolder "Off-white glasses OFF" "offwhite-glasses-women"

# Versace
Write-Host "`nProcessing Versace..." -ForegroundColor Magenta
Merge-ProductFolder "versace bag" "versace-bags-women"
Merge-ProductFolder "Versace glasses" "versace-glasses-women"
Merge-ProductFolder "Versace jewelry" "versace-jewelry-women"
Merge-ProductFolder "Versace Men clothes" "versace-clothes-men"
Merge-ProductFolder "Versace men's shoes" "versace-shoes-men"
Merge-ProductFolder "Versace The belt" "versace-belts-men"
Merge-ProductFolder "Versace Women clothes" "versace-clothes-women"
Merge-ProductFolder "Versace Women shoes" "versace-shoes-women"

# Yeezy
Write-Host "`nProcessing Yeezy..." -ForegroundColor Magenta
if (Test-Path "Yeezy") {
    Rename-Item "Yeezy" "yeezy-shoes-men"
}

Write-Host "`nReorganization complete!" -ForegroundColor Green
Write-Host "`nFinal folder list:" -ForegroundColor Cyan
Get-ChildItem | Select-Object Name | Sort-Object Name

Write-Host "`nNext step: Update ImportBrands.php to include new brands (Celine, Givenchy, McQueen, Moncler, Nike, Off-White, Versace, Yeezy)" -ForegroundColor Yellow
