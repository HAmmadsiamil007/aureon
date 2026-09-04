# AUREON / VINETA — Client WordPress Package

Premium WooCommerce frontend for WordPress. Single dynamic design
(**Vineta**, Khaadi-style store) built on the Aureon platform.

## What's in this folder

| Path | What it is | Install to |
|---|---|---|
| `aureon/` | WordPress **theme** ("Aureon" v1.2.0) | `wp-content/themes/aureon` |
| `aureon-studio/` | WordPress **plugin** ("Aureon Studio" v1.1.0) | `wp-content/plugins/aureon-studio` |
| `frontend/` | Shared **client frontend pack** (Vineta templates, bridge, Customizer sections, CSS/JS) | `wp-content/frontend` |
| `ferm-page.php` | Runtime router helper (also inside `aureon/`) | keep with theme |
| `docs/` | Architecture + forensic documentation | reference only |
| `HOW-TO-INSTALL.txt` | Step-by-step install guide | read first |

## Quick start

1. Read **`HOW-TO-INSTALL.txt`** (covers Docker demo, manual install,
   Customizer, content, troubleshooting).
2. Upload `aureon/`, `aureon-studio/`, `frontend/` into WordPress.
3. Activate theme **Aureon** + plugin **Aureon Studio** (WooCommerce required).
4. Save **Settings → Permalinks → Post name**.
5. Add your products/categories and set the hero in
   **Appearance → Customize → Vineta — Hero Banner**.

## Notes

- The store ships **empty on purpose** — all demo products/hero data were
  removed. Sections appear automatically once real content exists.
- Currency is **PKR (₨)**; change in WooCommerce → Settings → General.
- Colors: **Appearance → Customize → Vineta — Colors** (empty = template
  default coral scheme).
- Repo: https://github.com/HAmmadsiamil007/aureon — see `docs/` for the
  full release report and the frontend-edit workflow.
