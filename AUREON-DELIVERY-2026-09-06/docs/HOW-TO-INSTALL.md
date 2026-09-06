# AUREON / VINETA — HOW TO INSTALL (updated 2026-09-06)

**This file supersedes the 2026-09-04 install notes** (the older copies at `AUREON-WORDPRESS-DEPLOY/HOW-TO-INSTALL.txt` and `docs/docs/HOW-TO-INSTALL.md` remain as history; where they disagree with this file, this file wins — e.g. theme version is **3.6.1**, not 1.2.0).

Companion docs: `THEME-GUIDE.md` (themes/design packs) · `DOCKER.md` (one-command run) · `../DELIVERY-README.md` (package map).

## 1. What to upload (from `AUREON-WORDPRESS-DEPLOY/`)

| Source folder | Destination under `wp-content/` | Required |
|---|---|---|
| `themes/aureon/` | `themes/aureon/` | YES (Aureon theme 3.6.1) |
| `plugins/aureon-studio/` | `plugins/aureon-studio/` | YES (Aureon Studio 1.1.0) |
| `mu-plugins/ob-buffer.php` | `mu-plugins/ob-buffer.php` | YES (WC redirect buffering) |
| `frontend/` | `frontend/` | **YES — the engine + Vineta pack. Must sit at `wp-content/frontend/` exactly** |

Do **not** upload: `AUREON-GOLDEN-COPY/` (immutable baseline), `docs/`, `.serena/`, `test-results/`, `*.zip` archives.

## 2. Manual install (any host)

```bash
# after copying the four items above into wp-content/
wp plugin activate aureon-studio woocommerce
wp theme activate aureon
wp option update aether_active_design vineta
wp rewrite flush
```

Log in to WP admin → the Vineta design serves the storefront; Customizer controls for the pack live under Appearance → Customize (colors/hero/announcement/etc.).

## 3. Docker install (local demo)

```bash
cd AUREON-DELIVERY-2026-09-06
docker compose up -d
# open http://localhost:8080 — activation steps in DOCKER.md
```

## 4. Requirements

- WordPress 6.0+ · PHP 7.4+ (tested on PHP 8.2) · MySQL 5.7+/MariaDB
- WooCommerce (activates the shop/cart/checkout/account routes)
- Pretty permalinks ON (`wp rewrite flush` after activation — route map assumes `/product/…`, `/product-category/…`)

## 5. Verify the install (2-minute check)

1. Front page shows the **Vineta** design (frozen premium layout, coral accent).
2. `/shop/` lists products (demo products appear only if the catalog is empty — real products always win).
3. Header menu/footer menus come from **WP menus** (`primary`, `footer` locations) — not hardcoded.
4. Add to cart → badge count updates; `/checkout/` shows a real WooCommerce checkout.
5. `/nonexistent-page` serves the pack's **404.html** with a genuine HTTP 404.

If step 5 or the menus look wrong, re-check that `frontend/` sits at `wp-content/frontend/` and mu-plugins/ob-buffer.php is present — these are the two most common install mistakes.

## 6. Switching designs / themes later

See `THEME-GUIDE.md` — one option update, no core changes.

## 7. Integrity

The shipped tree is release candidate **RC-2026-09-06** (commit `1289995`; SHA-256 manifest for all 1,972 files in `AUREON-WORDPRESS-DEPLOY/../test-results/release-candidate-sha256.txt` and in git at `test-results/`). After any modification, regenerate the manifest and re-run the regression suite before delivering — a modified tree is a new release candidate.
