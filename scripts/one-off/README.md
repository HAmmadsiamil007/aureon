# one-off scripts — DO NOT RUN WITHOUT EXPLICIT APPROVAL

These scripts mutate a live WordPress database. They were quarantined here
(2026-09-06, audit fix T-02) so they cannot be executed accidentally or
served by a misconfigured web root. They are **not** part of the application
and must never be deployed.

| Script | Effect | Original location |
|---|---|---|
| `enable_cod.php` | Force-enables the WooCommerce Cash-on-Delivery gateway by writing `woocommerce_cod_settings` directly. Hardcoded `/var/www/html/wp-load.php`. | repo root |
| `update-contact.php` | Overwrites `aureon_settings` contact fields with placeholder data (phone `+92 300 1234567`, San Francisco address). | repo root |
| `rendered-home.EMPTY.txt` | Zero-byte file (`rendered-home.html`) left by an interrupted rendering test. Kept as evidence only; safe to delete. | repo root |

## Why quarantined

Forensic audit findings A-02 / F-003: config-mutating scripts in the
workspace root are (a) an accidental-execution risk against production and
(b) a potential unauthenticated config-exposure vector if the root is ever
web-served.

## Proper alternatives

- Payment gateways: configure in WP admin (WooCommerce → Settings → Payments) or via WP-CLI (`wp option update woocommerce_cod_settings --format=json`).
- Contact settings: use the Customizer / admin UI, not a script.
- Any future one-off: add it here with a README row and delete it after use.
