# Aureon — InfinityFree Deployment Status (UPDATED 2026-08-29)

## Target
- **Host:** InfinityFree hosting
- **URL:** `https://fermliving.wuaze.com/`
- **Remote path:** `/home/vol1_4/infinityfree.com/if0_39222017/fermliving.wuaze.com/htdocs/wp-content/`
- **Upload method:** FileZilla (FTP) — web file manager unreliable (333/390 files failed), 10 MB max file, 1 MB max PHP/HTML/JS

## What Was Deployed
- **Self-contained theme**: `AUREON-WORDPRESS-DEPLOY/aureon/` → upload to `wp-content/themes/`
- Theme is SELF-CONTAINED: `frontend/` engine bundled inside `aureon/frontend/`, only ONE folder uploads
- `install-ferm-products.php` → upload to WordPress root (`htdocs/`), visit in browser to create WC products/menus/pages, then delete

## Key Code Changes (synced to both DEPLOY + GOLDEN)
1. **Self-contained paths** (6 files): `frontend.php`, `assets.php`, `design.php`, `composer.php`, `aether-performance.php` — all use `get_template_directory_uri()`/`get_template_directory()` instead of `content_url()`/`WP_CONTENT_DIR`
2. **Default design**: `design.php` line 51: `'fermliving'` (was `'luxury'`)
3. **is_product() fatal fix**: `ferm-page.php` lines 200, 273 — wrapped with `function_exists('is_product') && is_product()`
4. **CDN CSS/JS/fonts included**: 1.4 MB in `cdn/shop/t/164/assets/` — layout/typography renders

## BLOCKER — Broken Hero/Product Images
- ALL 607 images in Ferm Living HTML reference `cdn/shop/files/...` paths
- These files were excluded from theme (172 MB)
- Downloading from Shopify CDN (`https://www.fermliving.com/cdn/shop/files/...`) returns 404 — images NOT publicly accessible
- **25 hero/collection banners** essential for site appearance (kitchen, living room, bedroom, etc.)
- **582 product/lifestyle images** — secondary, WC products will have own images after installer
- **Status: UNRESOLVED** — need either:
  - (a) Original image files from client/Shopify admin export
  - (b) Download from `fermliving.com` website HTML (if live)
  - (c) Use WooCommerce product images as replacement (installer creates placeholders)

## File Sizes
- DEPLOY folder: ~13.6 MB (theme) + installer + docs
- `AUREON-WORDPRESS-DEPLOY.zip`: 7.4 MB
- `aureon-theme.zip`: 6.3 MB
- GOLDEN folder: ~170 MB with extras (docs, reports)
- `AUREON-GOLDEN-COPY.zip`: ~170 MB

## Zip Packages (in `dyplo/` folder)
- `dyplo/AUREON-WORDPRESS-DEPLOY.zip` (7.4 MB)
- `dyplo/aureon-theme.zip` (6.3 MB)

## InfinityFree Setup Steps
1. Upload `aureon/` theme folder to `wp-content/themes/` via FileZilla
2. Activate theme in WP Admin → Appearance → Themes
3. Upload `install-ferm-products.php` to `htdocs/` (WP root)
4. Visit `https://fermliving.wuaze.com/install-ferm-products.php` in browser
5. Follow on-screen instructions, then delete installer
6. Set permalink structure: Settings → Permalinks → "Post name"
7. Flush OPcache: visit `/tmp/flush_opcache.php` or restart PHP

## Docker (local dev)
- Container: `aureon_wp` on port 8080
- DB: `aureon_db` (mysql:8.0)
- Admin: admin / admin123
- Flush OPcache: `docker exec aureon_wp php /tmp/flush_opcache.php`

## Git
- **Repo:** `https://github.com/HAmmadsiamil007/aureon.git`
- **Branch:** `main`
- **Freshly reinitialized** — old 444MB history dropped, no new push completed
- `**/cdn/` gitignored (too large)
- Remote: `origin` → `https://github.com/HAmmadsiamil007/aureon.git`

## Rules
- Both DEPLOY and GOLDEN must ALWAYS stay synced (MD5 verified)
- MD5 verify: `Get-FileHash` on key files between both folders
- DO NOT upload CDN images to GitHub (gitignored)
- InfinityFree 1 MB limit on PHP/HTML/JS — CDN CSS/JS already under limit individually
