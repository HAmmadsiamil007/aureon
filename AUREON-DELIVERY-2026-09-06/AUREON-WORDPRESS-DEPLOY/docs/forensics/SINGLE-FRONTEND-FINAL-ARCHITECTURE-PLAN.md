# SINGLE-FRONTEND-FINAL-ARCHITECTURE-PLAN

## 1. Architecture overview diagram
```
[ WordPress Core ] <-> [ WooCommerce ]
        |
[ Golden Core (ferm-page.php) ]
        |
[ Bridge / Adapters ]
        |
[ Client Pack (Vineta Frontend) ]
```

## 2. Layer responsibilities
- **Golden Core**: Route management, WP hooks, security.
- **Bridge/Adapter**: Translates WP/Woo data to frontend expected schema.
- **Client Pack**: Presentational only (HTML/CSS/JS).

## 3. Dynamic data contracts
- Woo -> Adapter -> JSON/Array -> Template.

## 4. Future-editability design
- All templates use variables injected by bridge, no direct `get_post()` calls.

## 5. What must NOT change
- `ferm-page.php` core engine.
