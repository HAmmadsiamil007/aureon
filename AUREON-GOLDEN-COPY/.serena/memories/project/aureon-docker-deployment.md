# Aureon — Docker Deployment Record (UPDATED 2026-08-05)

## Container: aureon_wp (RENAMED 2026-08-06 — was phantom-wp)
- **Image:** wordpress:latest (PHP 8.3.33), Apache mod_php
- **Port:** 8080 (http://localhost:8080)
- **MySQL:** aureon_db (mysql:8.0) — DB carried over from old phantom-db (site title still says "AETHER – Aureon Studio", active_plugins had woocommerce/woocommerce.php active but missing)
- **Admin:** admin / admin123
- **WooCommerce 11.0.0 installed 2026-08-06** (was active-but-missing; plugin WC module fatals `is_cart()` without it — MUST keep WC installed or harden woocommerce/functions/functions.php:158)
- **Deployed 2026-08-06:** current disk theme (145 files) + plugin (317 files), rolled-back state WITHOUT AETHER — container == disk, 0 aether refs, all pages 200

## CRITICAL DEPLOY LESSON (2026-08-07)
- PowerShell `Compress-Archive` zips write literal `\` path separators into entries. PHP `ZipArchive` on Linux extracts them as LITERAL FILENAME CHARACTERS — everything becomes junk like `components\commerce\rating.php` (one filename). The 2026-08-06 deploy worked because its payload (`/tmp/deploy.tar.gz`) was a TAR, not a zip.
- **USE TAR ALWAYS:** Windows `tar.exe` (bsdtar) creates forward-slash archives: `tar -czf out.tar.gz -C <dir> .` → base64 pipe → in-container `tar -xzf out.tar.gz -C <target>`. Supports `--exclude=source` for payload trimming.
- Cleanup of accidentally-extracted backslash junk: `find <dir> -depth -name '*\\*' -delete`.
- `rm -rf` of themes/aureon + plugins/aureon-studio can fail ("Device or resource busy" — Apache). Extract OVER the dir instead, then delete backslash junk.

## Deployment Method (UPDATED 2026-08-07 — docker cp WORKS for single files)
`docker cp` works fine for individual small files (used for section-checkout.php, main.js,
wc-diag.php on 2026-08-07). Bulk directory deploys are still unreliable → use tar.gz
method below. Prefer tar over zip ALWAYS (see CRITICAL DEPLOY LESSON above).

```powershell
# 1. Zip locally (from temp dir)
# 2. Base64-encode and pipe into container:
$b64 = [Convert]::ToBase64String([IO.File]::ReadAllBytes("C:\path\to\files.zip"))
Set-Content -Path "C:\Users\hamma\AppData\Local\Temp\opencode\payload.b64" -Value $b64 -NoNewline -Encoding Ascii
cmd /c "type C:\Users\hamma\AppData\Local\Temp\opencode\payload.b64 | docker exec -i phantom-wp sh -c ""base64 -d > /tmp/payload.zip"""
# 3. Unzip inside container
docker exec phantom-wp sh -c "cd /var/www/html/wp-content && unzip -o /tmp/payload.zip"
```

For single files, pipe the base64 directly:
```powershell
$b64 = [Convert]::ToBase64String([IO.File]::ReadAllBytes("C:\path\to\file.php"))
Set-Content -Path "C:\Users\hamma\AppData\Local\Temp\opencode\file.b64" -Value $b64 -NoNewline -Encoding Ascii
cmd /c "type C:\Users\hamma\AppData\Local\Temp\opencode\file.b64 | docker exec -i phantom-wp sh -c ""base64 -d > /var/www/html/wp-content/plugins/aureon-studio/file.php"""
```

## Permissions (CRITICAL after zip deploy)
Zips strip execute bits on directories → 403 errors. ALWAYS run:
```bash
docker exec phantom-wp chmod -R a+rX /var/www/html/wp-content/themes/aureon /var/www/html/wp-content/plugins/aureon-studio
docker exec phantom-wp chown -R www-data:www-data /var/www/html/wp-content/themes/aureon /var/www/html/wp-content/plugins/aureon-studio
```

## Browser Caching (CRITICAL for verification)
The customizer JS has `?ver=3.6.1` query strings — a stale cached file masked a real fix once. When re-deploying JS:
1. Use Playwright `Network.clearBrowserCache` + `setCacheDisabled`
2. Verify the served file content in-container (`grep` for expected strings) — disk truth beats browser truth

## Phase 17 Deploy (2026-08-07) — LIVE
- Full Phase 17 stack deployed via tar.gz method: themes/aureon + plugins/aureon-studio + wp-content/frontend (excl. source/). Verified 200 + 0 console errors + tokens inline CSS printing.
- Live bug fixed during deploy: `wp_enqueue_style('aether-tokens', false)` never registers handle (WP skips add() when src falsy) → WP 6.9.1 drops dependents (aether-frontend/a11y/motion). Fixed with `wp_register_style()` — see aureon/theme/inc/frontend.php:117-121.
- Hero default enriched 1→3 slides (frontend/tokens/tokens.php).
- GAP: mu-plugins/aureon-fix-wc-session.php absent from disk + container (old record said deployed; the 2026-08-06 deploy.tar.gz did not include mu-plugins).

## Deployed Paths
```
/var/www/html/wp-content/
├── themes/
│   └── aureon/           (deployed)
├── plugins/
│   ├── aureon-studio/    (deployed)
│   └── woocommerce/      (installed, active)
```

## Version Constants (Disk Truth)
- Theme: `AUREON_VERSION = '3.6.1'`, display Version: 1.0.0
- Plugin: `AUREON_STUDIO_VERSION = '3.0.0'`, display Version: 1.0.0

## Live Verification Status (2026-08-05) ✅
- Customizer (`/wp-admin/customize.php`) loads with **0 console errors**, 1 WP-core sandbox warning
- React Font Manager ("Add Font"), Typography Manager ("Add Typography") render
- Global Colors palette renders (contrast/base/accent swatches)
- Plugin typography groups inject (Secondary Navigation, WooCommerce) via `aureonProCustomizerControls`
- Footer shows "© 2026 Aureon • Built with Aureon" → aureonstudio.com
- The `generateWooCommerce is not defined` error seen earlier = stale browser cache of pre-rename file — container file verified clean

## Git
- **Repo:** https://github.com/HAmmadsiamil007/aureon.git
- **Latest commit:** 76acd5e (uncommitted changes exist — see aureon-complete-record)
