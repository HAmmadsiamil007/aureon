# LOCAL RUNTIME ACTIVATION — ADDENDUM (2026-09-06)

**Event:** The previously-missing WordPress runtime (blocker B-1, local scope) now **exists locally via Docker** and runs the exact release candidate.

## What was done

1. **Discovered the live stack** (`wordpress-wordpress-1` + `wordpress-db-1` + `wordpress-phpmyadmin-1`) was bind-mounted to the **stale `aureon/` tree** — the 5-day-old copy my audit flagged, not the RC.
2. **Reconstructed the deleted root `docker-compose.yml`** (recovered DB creds `wordpress`/`wordpress`@`db:3306`, volumes `wordpress_wp_data` / `wordpress_db_data`, phpMyAdmin on 8081) and **re-pointed the engine binds to the canonical `AUREON-WORDPRESS-DEPLOY` RC tree**: `frontend/`, `themes/aureon`, `plugins/aureon-studio`, `mu-plugins/`.
3. **Recreated only the WordPress container** (`docker compose up -d --force-recreate wordpress`). DB volume reused — all data, plugins, and uploads survived.
4. **Hash-proof:** container's `composer.php` = `71b33c05…`, `ferm-page.php` = `5920225c…` — **byte-identical to the tested RC**.
5. **Restored** the one file my earlier mount-side `docker cp` had disturbed in the `aureon/` archive tree (`git checkout`), keeping the archive plan intact.

## Gates this converts to PASS (local scope — see L-01…L-10 in the acceptance matrix)

| Gate | Live evidence |
|---|---|
| Routes `/` `/shop` `/cart` `/checkout` `/my-account` `/product/{slug}` `/product-category/{slug}` `/blog` | all HTTP 200 with correct Vineta identity (slots: `hero`, `global.logo/navigation/search/newsletter/social`, `shop.product_grid`) |
| `/404` | **genuine HTTP 404** + VinetaPageData — T-12 fix proven live |
| Cart E2E | add-to-cart #577 → session cart renders product, 12× quantity, 3× subtotal |
| Checkout surface | real WC engine: `billing` ×30, `place_order`, `woocommerce-checkout` |
| Auth template | `form.vt-auth-form` + username/password + genuine `woocommerce-login-nonce` |
| Search | `?s=kurta&post_type=product` → 200, 3 live matches |
| Blog | archive serves the real `hello-world` post in vineta context |
| Runtime-vs-RC integrity | engine hashes byte-identical after re-point |
| PHP error log | zero Aureon fatals; only third-party WC notices |
| Product/category | `product.variation` slot + `tf-product-info` + add_to_cart; Women category context |

## What stays BLOCKED

- **L-11** order placement (checkout POST + payment method) — next local execution pass
- **R-05** variable-product client test — still N/A (no variable product in catalog)
- **R-15/R-16** menus live-edit, Customizer round-trip — need WP-admin interaction
- **R-18/R-19/R-20** console/network, responsive, a11y — need a full browser session
- **R-22/R-23/R-24** production smoke, real SMTP, payment sandbox — external environment only

## Release rule (unchanged)

The **tested RC `1289995` is untouched** — this was a runtime/environment change only, zero source modifications. The compose file is committed as infrastructure, not application code. Verdict remains `AUREON_CLIENT_PRODUCTION_READY_BLOCKED` until production gates run, but the project now has a **running, hash-verified local environment** for executing the remaining local gates.
