# DOCKER — AUREON/Vineta in one command

## Quick start

```bash
cd AUREON-DELIVERY-2026-09-06
docker compose up -d
```

Then open **http://localhost:8080**.

The `docker-compose.yml` bind-mounts the deployment tree straight into WordPress:

| Mount | Target in container | Purpose |
|---|---|---|
| `AUREON-WORDPRESS-DEPLOY/themes/` | `wp-content/themes/` | Aureon theme 3.6.1 |
| `AUREON-WORDPRESS-DEPLOY/plugins/` | `wp-content/plugins/` | Aureon Studio 1.1.0 |
| `AUREON-WORDPRESS-DEPLOY/mu-plugins/` | `wp-content/mu-plugins/` | `ob-buffer.php` (required) |
| `AUREON-WORDPRESS-DEPLOY/frontend/` | `wp-content/frontend/` | **AETHER engine + Vineta pack (required)** |

WordPress core + the database live in named volumes (`wordpress_data`, `db_data`) so the release tree stays read-only to the runtime.

## First-run activation (inside the container)

```bash
# WordPress core download finishes during first start; then activate:
docker exec -it aureon-wordpress-1 bash
apt-get update && apt-get install -y curl less && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp

wp core install --url=http://localhost:8080 --title="Aureon Store" --admin_user=admin --admin_password=CHANGE-ME --admin_email=admin@example.com --skip-email
wp plugin activate aureon-studio woocommerce
wp theme activate aureon
wp option update aether_active_design vineta
wp rewrite flush
```

## Switching designs

```bash
wp option update aether_active_design <slug>   # any pack in wp-content/frontend/designs/
```

## Reference install already running

A live reference container (same compose shape, name `wordpress-wordpress-1`, port 8080) was used to verify this package: theme `aureon` active, design `vineta`, all engine files mounted under `wp-content/frontend/`. If you already run it, refresh its engine files from this package before testing (see DELIVERY-README.md → integrity).

## Stop / reset

```bash
docker compose down          # keeps volumes
docker compose down -v       # full reset (database + core)
```
