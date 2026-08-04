# Aureon Rebrand Script - Simplified
$ErrorActionPreference = "Stop"
$theme = "C:\Users\hamma\Downloads\wordpress\aureon\theme"
$plugin = "C:\Users\hamma\Downloads\wordpress\aureon\plugin"

Write-Host "=== AUREON REBRAND ===" -ForegroundColor Cyan

# Phase 1: Sentinel protection
Write-Host "Phase 1: Sentinel protection..." -ForegroundColor Yellow
$sentinelMap = @(
    @("GENERATEBLOCKS", "@@GENERATEBLOCKS@@"),
    @("GenerateBlocks", "@@GenerateBlocks@@"),
    @("generateblocks", "@@generateblocks@@"),
    @("regenerate", "@@regenerate@@"),
    @("generated", "@@generated@@")
)

$phpFiles = Get-ChildItem -Path $theme,$plugin -Recurse -Include *.php,*.js,*.css,*.txt
$protected = 0
foreach ($f in $phpFiles) {
    $c = Get-Content $f.FullName -Raw -ErrorAction SilentlyContinue
    if (-not $c) { continue }
    $orig = $c
    foreach ($rule in $sentinelMap) { $c = $c.Replace($rule[0], $rule[1]) }
    if ($c -ne $orig) { Set-Content $f.FullName $c -NoNewline; $protected++ }
}
Write-Host "  Protected $protected files" -ForegroundColor Green

# Phase 2: Token replacement
Write-Host "Phase 2: Token replacement..." -ForegroundColor Yellow
$replacements = @(
    @("GeneratePress Premium", "Aureon Studio"),
    @("GP Premium", "Aureon Studio"),
    @("generatepress.com", "aureonstudio.com"),
    @("docs.generatepress.com", "docs.aureonstudio.com"),
    @("sites.generatepress.com", "sites.aureonstudio.com"),
    @("gpsites.co", "sites.aureonstudio.com"),
    @("generatepress", "aureon"),
    @("GeneratePress", "Aureon"),
    @("GP_PREMIUM_VERSION", "AUREON_STUDIO_VERSION"),
    @("GP_PREMIUM_DIR_PATH", "AUREON_STUDIO_DIR_PATH"),
    @("GP_PREMIUM_DIR_URL", "AUREON_STUDIO_DIR_URL"),
    @("GP_PREMIUM", "AUREON_STUDIO"),
    @("gp_premium", "aureon_studio"),
    @("gp-premium", "aureon-studio"),
    @("GP_LIBRARY_DIRECTORY", "AUREON_LIBRARY_DIRECTORY"),
    @("GP_LIBRARY", "AUREON_LIBRARY"),
    @("GP_VERSION", "AUREON_VERSION"),
    @("GP_", "AUREON_"),
    @("gp_", "aureon_"),
    @("GP-", "AUREON-"),
    @("gp-", "aureon-"),
    @("gpp-", "aureon-studio-"),
    @("gppVersion", "aureonStudioVersion"),
    @("gppSiteLibrary", "aureonStudioSiteLibrary"),
    @("GP One", "Aureon One"),
    @("GENERATE_", "AUREON_"),
    @("Generate_", "Aureon_"),
    @("generate_", "aureon_"),
    @("generate-", "aureon-"),
    @("Tom Usborne", "Aureon Studio"),
    @("EDGE22", "AUREON"),
    @("gen_premium_license_key", "aureon_studio_license_key"),
    @("generate_db_version", "aureon_db_version"),
    @("theme_mods_generatepress", "theme_mods_aureon")
)

$phpFiles = Get-ChildItem -Path $theme,$plugin -Recurse -Include *.php,*.js,*.css,*.txt
$modified = 0
foreach ($f in $phpFiles) {
    $c = Get-Content $f.FullName -Raw -ErrorAction SilentlyContinue
    if (-not $c) { continue }
    $orig = $c
    foreach ($rule in $replacements) { $c = $c.Replace($rule[0], $rule[1]) }
    if ($c -ne $orig) { Set-Content $f.FullName $c -NoNewline; $modified++ }
}
Write-Host "  Modified $modified files" -ForegroundColor Green

# Phase 3: Restore sentinels
Write-Host "Phase 3: Restoring sentinels..." -ForegroundColor Yellow
$restoreMap = @(
    @("@@GENERATEBLOCK@@", "GENERATEBLOCK"),
    @("@@GenerateBlocks@@", "GenerateBlocks"),
    @("@@generateblocks@@", "generateblocks"),
    @("@@regenerate@@", "regenerate"),
    @("@@generated@@", "generated")
)

$phpFiles = Get-ChildItem -Path $theme,$plugin -Recurse -Include *.php,*.js,*.css,*.txt
$restored = 0
foreach ($f in $phpFiles) {
    $c = Get-Content $f.FullName -Raw -ErrorAction SilentlyContinue
    if (-not $c) { continue }
    $orig = $c
    foreach ($rule in $restoreMap) { $c = $c.Replace($rule[0], $rule[1]) }
    if ($c -ne $orig) { Set-Content $f.FullName $c -NoNewline; $restored++ }
}
Write-Host "  Restored $restored files" -ForegroundColor Green

# Phase 4: File renames
Write-Host "Phase 4: File renames..." -ForegroundColor Yellow
$renames = @(
    @("gp-premium.php", "aureon-studio.php"),
    @("backgrounds\generate-backgrounds.php", "backgrounds\aureon-backgrounds.php"),
    @("blog\generate-blog.php", "blog\aureon-blog.php"),
    @("colors\generate-colors.php", "colors\aureon-colors.php"),
    @("copyright\generate-copyright.php", "copyright\aureon-copyright.php"),
    @("disable-elements\generate-disable-elements.php", "disable-elements\aureon-disable-elements.php"),
    @("hooks\generate-hooks.php", "hooks\aureon-hooks.php"),
    @("menu-plus\generate-menu-plus.php", "menu-plus\aureon-menu-plus.php"),
    @("page-header\generate-page-header.php", "page-header\aureon-page-header.php"),
    @("secondary-nav\generate-secondary-nav.php", "secondary-nav\aureon-secondary-nav.php"),
    @("sections\generate-sections.php", "sections\aureon-sections.php"),
    @("spacing\generate-spacing.php", "spacing\aureon-spacing.php"),
    @("typography\generate-fonts.php", "typography\aureon-fonts.php"),
    @("library\customizer\controls\js\generatepress-controls.js", "library\customizer\controls\js\aureon-controls.js")
)

foreach ($rule in $renames) {
    $old = Join-Path $plugin $rule[0]
    $new = Join-Path $plugin $rule[1]
    if (Test-Path $old) { Rename-Item $old (Split-Path $new -Leaf); Write-Host "  $($rule[0]) -> $($rule[1])" }
}

$fontRenames = @(
    @("assets\fonts\generatepress.eot", "assets\fonts\aureon.eot"),
    @("assets\fonts\generatepress.svg", "assets\fonts\aureon.svg"),
    @("assets\fonts\generatepress.ttf", "assets\fonts\aureon.ttf"),
    @("assets\fonts\generatepress.woff", "assets\fonts\aureon.woff"),
    @("assets\fonts\generatepress.woff2", "assets\fonts\aureon.woff2")
)
foreach ($rule in $fontRenames) {
    $old = Join-Path $theme $rule[0]
    if (Test-Path $old) { Rename-Item $old (Split-Path $rule[1] -Leaf) }
}

$iconRenames = @(
    @("general\icons\gp-premium.eot", "general\icons\aureon-studio.eot"),
    @("general\icons\gp-premium.svg", "general\icons\aureon-studio.svg"),
    @("general\icons\gp-premium.ttf", "general\icons\aureon-studio.ttf"),
    @("general\icons\gp-premium.woff", "general\icons\aureon-studio.woff")
)
foreach ($rule in $iconRenames) {
    $old = Join-Path $plugin $rule[0]
    if (Test-Path $old) { Rename-Item $old (Split-Path $rule[1] -Leaf) }
}

$langFiles = Get-ChildItem -Path "$plugin\langs" -Filter "gp-premium-*" -ErrorAction SilentlyContinue
foreach ($f in $langFiles) {
    $newName = $f.Name -replace "^gp-premium-", "aureon-studio-"
    Rename-Item $f.FullName $newName
}

Write-Host "  File renames complete" -ForegroundColor Green

# Phase 5: Branding
Write-Host "Phase 5: Branding..." -ForegroundColor Yellow

# Write style.css
$styleContent = "/*" + [Environment]::NewLine + "Theme Name: Aureon" + [Environment]::NewLine + "Theme URI: #" + [Environment]::NewLine + "Author: Aureon Studio" + [Environment]::NewLine + "Author URI: #" + [Environment]::NewLine + "Description: A lightweight WordPress theme built for developers." + [Environment]::NewLine + "Version: 1.0.0" + [Environment]::NewLine + "Requires at least: 6.0" + [Environment]::NewLine + "Tested up to: 6.8" + [Environment]::NewLine + "Requires PHP: 7.4" + [Environment]::NewLine + "License: GNU General Public License v2 or later" + [Environment]::NewLine + "License URI: http://www.gnu.org/licenses/gpl-2.0.html" + [Environment]::NewLine + "Text Domain: aureon" + [Environment]::NewLine + "*/"
Set-Content "$theme\style.css" $styleContent -NoNewline

# Write license.txt files
$gplText = "Aureon Theme" + [Environment]::NewLine + "Copyright (c) 2026 Aureon Studio" + [Environment]::NewLine + [Environment]::NewLine + "This program is free software; you can redistribute it and/or modify" + [Environment]::NewLine + "it under the terms of the GNU General Public License, version 2, as" + [Environment]::NewLine + "published by the Free Software Foundation." + [Environment]::NewLine + [Environment]::NewLine + "This program is distributed in the hope that it will be useful," + [Environment]::NewLine + "but WITHOUT ANY WARRANTY; without even the implied warranty of" + [Environment]::NewLine + "MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the" + [Environment]::NewLine + "GNU General Public License for more details." + [Environment]::NewLine + [Environment]::NewLine + "You should have received a copy of the GNU General Public License" + [Environment]::NewLine + "along with this program; if not, write to the Free Software" + [Environment]::NewLine + "Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA"
Set-Content "$theme\license.txt" $gplText -NoNewline

$pluginGplText = "Aureon Studio Plugin" + [Environment]::NewLine + "Copyright (c) 2026 Aureon Studio" + [Environment]::NewLine + [Environment]::NewLine + "This program is free software; you can redistribute it and/or modify" + [Environment]::NewLine + "it under the terms of the GNU General Public License, version 2, as" + [Environment]::NewLine + "published by the Free Software Foundation." + [Environment]::NewLine + [Environment]::NewLine + "This program is distributed in the hope that it will be useful," + [Environment]::NewLine + "but WITHOUT ANY WARRANTY; without even the implied warranty of" + [Environment]::NewLine + "MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the" + [Environment]::NewLine + "GNU General Public License for more details." + [Environment]::NewLine + [Environment]::NewLine + "You should have received a copy of the GNU General Public License" + [Environment]::NewLine + "along with this program; if not, write to the Free Software" + [Environment]::NewLine + "Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA"
Set-Content "$plugin\license.txt" $pluginGplText -NoNewline

Write-Host "  Branding complete" -ForegroundColor Green

# Phase 6: Neutralize endpoints
Write-Host "Phase 6: Neutralizing endpoints..." -ForegroundColor Yellow
$endpointFiles = @(
    "$plugin\site-library\class-site-library-rest.php",
    "$plugin\site-library\class-site-library-helper.php",
    "$plugin\inc\legacy\activation.php"
)
foreach ($f in $endpointFiles) {
    if (Test-Path $f) {
        $c = Get-Content $f -Raw
        $c = $c -replace "https://sites\.aureonstudio\.com[^\x22]+", "https://example.com/invalid"
        $c = $c -replace "https://gpsites\.co[^\x22]+", "https://example.com/invalid"
        $c = $c -replace "https://aureonstudio\.com", "https://example.com"
        $c = $c -replace "https://docs\.aureonstudio\.com", "https://example.com"
        Set-Content $f $c -NoNewline
    }
}
Write-Host "  Endpoints neutralized" -ForegroundColor Green

# Phase 7: Fix JS variables
Write-Host "Phase 7: Fixing JS variables..." -ForegroundColor Yellow
$jsFixes = @(
    @("gpSmoothScroll", "aureonSmoothScroll"),
    @("gpControls", "aureonControls"),
    @("gpButtonActions", "aureonButtonActions"),
    @("gpPremiumEditor", "aureonStudioEditor"),
    @("gpPostMessageFields", "aureonPostMessageFields"),
    @("gpPostMessageStylesOutput", "aureonPostMessageStylesOutput"),
    @("gpPostMessage", "aureonPostMessage"),
    @("gpCustomizerControls", "aureonCustomizerControls"),
    @("gpFontLibraryURI", "aureonFontLibraryURI"),
    @("gpFontLibrary", "aureonFontLibrary"),
    @("gpVersion", "aureonVersion"),
    @("gpPremiumBlockElements", "aureonPremiumBlockElements"),
    @("generateDashboard", "aureonDashboard"),
    @("gppFontLibrary", "aureonFontLibrary"),
    @("data-gpmodal-close", "data-aureonmodal-close"),
    @("gpscroll", "aureonscroll")
)

$allFiles = Get-ChildItem -Path $theme,$plugin -Recurse -Include *.php,*.js,*.css
$jsFixed = 0
foreach ($f in $allFiles) {
    $c = Get-Content $f.FullName -Raw -ErrorAction SilentlyContinue
    if (-not $c) { continue }
    $orig = $c
    foreach ($rule in $jsFixes) { $c = $c.Replace($rule[0], $rule[1]) }
    if ($c -ne $orig) { Set-Content $f.FullName $c -NoNewline; $jsFixed++ }
}
Write-Host "  Fixed $jsFixed files" -ForegroundColor Green

# Phase 8: Fix docblocks
Write-Host "Phase 8: Fixing docblocks..." -ForegroundColor Yellow
$docblockFixes = @(
    @("$theme\inc\class-rest.php", "@package GenerateBlocks", "@package Aureon"),
    @("$theme\inc\class-rest.php", "Class GenerateBlocks_Rest", "Class Aureon_Rest"),
    @("$plugin\inc\class-rest.php", "@package GenerateBlocks", "@package Aureon Studio"),
    @("$plugin\inc\class-rest.php", "Class GenerateBlocks_Rest", "Class Aureon_Pro_Rest")
)
foreach ($fix in $docblockFixes) {
    if (Test-Path $fix[0]) {
        $c = Get-Content $fix[0] -Raw
        $c = $c.Replace($fix[1], $fix[2])
        Set-Content $fix[0] $c -NoNewline
    }
}
Write-Host "  Docblocks fixed" -ForegroundColor Green

# Phase 9: WPML config
Write-Host "Phase 9: WPML config..." -ForegroundColor Yellow
$wpmlConfig = "$plugin\wpml-config.xml"
if (Test-Path $wpmlConfig) {
    $c = Get-Content $wpmlConfig -Raw
    $c = $c -replace "theme_mods_generatepress", "theme_mods_aureon"
    $c = $c -replace "_generate_", "_aureon_"
    Set-Content $wpmlConfig $c -NoNewline
}
Write-Host "  WPML config updated" -ForegroundColor Green

# Phase 10: PHP syntax check
Write-Host "Phase 10: PHP syntax check..." -ForegroundColor Yellow
$errs = 0
$total = 0
Get-ChildItem -Path $theme,$plugin -Recurse -Include *.php | ForEach-Object {
    $total++
    php -l $_.FullName 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0) { $errs++; Write-Host "  ERROR: $($_.Name)" -ForegroundColor Red }
}
Write-Host "  Total: $total files, $errs errors"
if ($errs -eq 0) { Write-Host "  ALL PHP FILES VALID" -ForegroundColor Green }

# Phase 11: Final verification
Write-Host "Phase 11: Final verification..." -ForegroundColor Yellow
$allFiles = Get-ChildItem -Path $theme,$plugin -Recurse -Include *.php,*.js,*.css,*.txt
$gp = $allFiles | Select-String -Pattern "generatepress|gp-premium|gp_premium" | Where-Object { $_.Path -notlike "*license.txt*" }
Write-Host "  GP tokens outside license.txt: $($gp.Count)"

$gb = ($allFiles | Select-String -Pattern "GenerateBlocks" | Measure-Object).Count
$rg = ($allFiles | Select-String -Pattern "regenerate" | Measure-Object).Count
$gn = ($allFiles | Select-String -Pattern "generated" | Measure-Object).Count
Write-Host "  GenerateBlocks: $gb, regenerate: $rg, generated: $gn"

Write-Host ""
Write-Host "=== REBRAND COMPLETE ===" -ForegroundColor Green
