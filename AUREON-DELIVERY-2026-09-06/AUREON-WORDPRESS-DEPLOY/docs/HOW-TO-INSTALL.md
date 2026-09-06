====================================================================
 AUREON / VINETA — CLIENT WORDPRESS PACKAGE
 HOW TO INSTALL (Step-by-Step)
====================================================================
 Package     : Aureon theme v1.2.0 + Aureon Studio plugin v1.1.0
 Frontend    : Vineta premium client frontend (Khaadi-style store)
 Build date  : 2026-09-04
 Repository  : https://github.com/HAmmadsiamil007/aureon
 Demo run    : http://localhost:8080  (Docker, see Section 3)
====================================================================

--------------------------------------------------------------------
 1. WHAT IS INSIDE THIS FOLDER
--------------------------------------------------------------------
 Copy this ENTIRE folder to your server / computer. It contains:

   aureon/            -> The WordPress THEME (folder name = theme slug)
                         Upload to:  wp-content/themes/aureon
                         Activate as: "Aureon"

   aureon-studio/     -> The WordPress PLUGIN (all Aureon modules)
                         Upload to:  wp-content/plugins/aureon-studio
                         Activate as: "Aureon Studio"

   frontend/          -> The shared client FRONTEND PACK (Vineta design,
                         templates, bridge, CSS/JS, Customizer sections)
                         Upload to:  wp-content/frontend
                         (Keep the folder name "frontend" — the theme
                         resolves it by that exact name.)

   ferm-page.php      -> Runtime router helper. Already included inside
                         aureon/ too; keep it inside the theme folder.
                         Do NOT delete it.

   docs/              -> Full architecture + forensic documentation
                         (reference only — no need to upload to wp-content)

   HOW-TO-INSTALL.txt -> This file.

--------------------------------------------------------------------
 2. REQUIREMENTS
--------------------------------------------------------------------
 - WordPress 6.x or newer
 - WooCommerce 8.9 or newer (required for shop/cart/checkout)
 - PHP 7.4+ (PHP 8.0/8.1/8.2 recommended)
 - MySQL / MariaDB
 - Pretty permalinks enabled (Settings > Permalinks > "Post name")

--------------------------------------------------------------------
 3. QUICK DEMO WITH DOCKER (this repository only)
--------------------------------------------------------------------
 From the repository root (where docker-compose.yml lives):

   docker compose up -d

 Then open:

   Store   : http://localhost:8080
   Admin   : http://localhost:8080/wp-admin

 The live theme/plugin/frontend are bind-mounted from ./aureon/* so any
 file change is reflected immediately.

--------------------------------------------------------------------
 4. STANDARD INSTALLATION (manual WordPress)
--------------------------------------------------------------------
 STEP 1 — Upload the theme
   Copy the "aureon" folder into:  wp-content/themes/
   Result: wp-content/themes/aureon/style.css must exist.

 STEP 2 — Upload the plugin
   Copy the "aureon-studio" folder into:  wp-content/plugins/
   Result: wp-content/plugins/aureon-studio/aureon-studio.php must exist.

 STEP 3 — Upload the frontend pack
   Copy the "frontend" folder into:  wp-content/
   Result: wp-content/frontend/designs/vineta/manifest.json must exist.

 STEP 4 — Activate
   Appearance > Themes        -> Activate "Aureon"
   Plugins > Installed Plugins -> Activate "Aureon Studio"
   (WooCommerce must also be active.)

 STEP 5 — Permalinks (very important)
   Settings > Permalinks -> choose "Post name" -> Save.
   This flushes rewrite rules so /shop, /product/..., /cart, etc. work.

 STEP 6 — WooCommerce pages
   WooCommerce > Status > Tools > "Create default pages" (if needed).
   Or WooCommerce > Settings > Advanced > Page setup:
     Shop page      = /shop
     Cart page      = /cart
     Checkout page  = /checkout
     My account     = /my-account

 STEP 7 — Menus
   Appearance > Menus -> create/assign your menu to the header location.
   The header + footer menus are server-rendered from WordPress, so they
   appear on every page and in the Customizer preview automatically.

 STEP 8 — Verify
   Open the homepage. Hero/products/category sections appear as soon as
   real content exists (see Section 5). The site ships with an EMPTY
   store on purpose — all demo data was removed.

--------------------------------------------------------------------
 5. ADD YOUR CONTENT
--------------------------------------------------------------------
 Products
   WooCommerce > Products > Add New
     - Title, description
     - Product data: Regular price (store currency, see Section 7)
     - Product image(s)
     - Category
     - Stock status

 Categories
   WooCommerce > Products > Categories
   The homepage "Categories" tabs render your REAL categories
   automatically (Women / Men + children). No code change needed.

 Media
   Media > Library (all uploaded images)

--------------------------------------------------------------------
 6. CUSTOMIZER (Appearance > Customize)
--------------------------------------------------------------------
 Vineta — Hero Banner
   - "Hero slides" repeater: add slides with
       Headline, Subline, Badge, both CTA buttons,
       Desktop/Laptop image, Tablet image, Mobile image, alt, overlay.
   - Save -> homepage banner updates instantly.
   - Leave empty = hero section hidden (no frozen demo images).

 Vineta — Colors
   - Accent, Accent hover, Page background, Surface,
     Primary text, Secondary text, Borders.
   - Empty fields = template default scheme (coral #ff6f61, Poppins).
   - Only the colors you pick are applied (CSS variable overrides).

 Site Identity
   - Logo, Site icon (favicon), Site title, Tagline.

 Menus
   - Assign menus for header (desktop + mobile) and footer.

 Tip: after saving a Customizer change, reload the preview once — the
 nav menu is server-rendered, which makes it stable in the preview.

--------------------------------------------------------------------
 7. CURRENCY & PRICES
--------------------------------------------------------------------
 Store currency: PKR (₨). Prices are entered in WooCommerce as-is.
 To change: WooCommerce > Settings > General > Currency.
 The frontend formats prices automatically (₨ symbol + thousands
 separator); if you change the currency, update the setting and the
 frontend formatter will follow the WooCommerce symbol.

--------------------------------------------------------------------
 8. WHERE THE DESIGN LIVES (frontend pack)
--------------------------------------------------------------------
 frontend/designs/vineta/
   index.html          Homepage template (hero, category tabs, sellers)
   product-detail.html Product page
   shop-default.html   Shop/category layout
   checkout.html, account-*.html, blog-*.html, 404.html, ...
   css/  js/           Styles + bridge scripts
   composer.php        Pack-level bridge: page data, dynamic slots,
                       server-rendered menus, Customizer sections
   tokens.php          Pack tokens (announcement defaults etc.)
   manifest.json       Pack manifest

 Future design edits = change these templates/CSS only.
 Keep the dynamic slots / data attributes intact so WooCommerce,
 Customizer, menus, cart and search stay dynamic.
 (See docs/architecture/UNIVERSAL-SAFE-FRONTEND-DESIGN-EDIT-WORKFLOW.md)

--------------------------------------------------------------------
 9. TROUBLESHOOTING
--------------------------------------------------------------------
 Blank / white page
   - Check the PHP error log (wp-content/debug.log with WP_DEBUG on).
   - Confirm "Aureon Studio" and "WooCommerce" are ACTIVE.
   - Re-save Settings > Permalinks.

 Homepage sections are missing / empty
   - Expected when there are no products/categories yet. Add real
     content (Section 5) — sections reappear automatically.
   - Hero hidden until you add slides in Customizer > Vineta — Hero
     Banner.

 Menu not showing
   - Assign the menu in Appearance > Menus (header location).
   - Hard-refresh (Ctrl+F5); the menu is server-rendered in the HTML.

 Images broken
   - Upload real media (Media > Library) and set it on products/slides.

 Prices look wrong / currency symbol
   - WooCommerce > Settings > General > Currency (Section 7).

 Colors turned "black" or unexpected
   - Clear the Vineta — Colors fields (empty = template default).

 "Product not found" on /product/...
   - That product was removed with the demo wipe; add your own.

--------------------------------------------------------------------
 10. UPDATING THE PACKAGE LATER
--------------------------------------------------------------------
 1. Back up: wp-content/themes/aureon, wp-content/plugins/aureon-studio,
    wp-content/frontend and your database.
 2. Replace the three folders with the new release.
 3. Re-save Settings > Permalinks.
 4. Clear any page cache.

--------------------------------------------------------------------
 11. VERSION RECORD
--------------------------------------------------------------------
 Theme          : Aureon 1.2.0
 Plugin         : Aureon Studio 1.1.0
 Frontend       : Vineta (single active design)
 WooCommerce    : 8.9.0 (tested)
 Store state    : empty catalog — client adds own products/categories
 Release folder: AUREON-WORDPRESS-DEPLOY  (deploy candidate)
 Baseline      : AUREON-GOLDEN-COPY       (identical, protected copy)

 Git: master @ 3c2571d (https://github.com/HAmmadsiamil007/aureon)
====================================================================
