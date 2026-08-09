# G6 — Hero Slides Repeater (Aureon v1.2.0)

> **Date:** 2026-08-09 · **Scope:** Customizer repeater for `aether_hero_slides` · **Type:** Theme customizer infrastructure (G6 closure)

## 1. Context

The AETHER hero is fully dynamic end-to-end (adapter → section → component) except for one thing: **the slides themselves have no Customizer control.** `aureon_settings['aether_hero_slides']` is only settable via the token defaults (`frontend/tokens/tokens.php`) or raw option writes. STATUS.md:270 claims a "hero-slides JSON textarea + `aureon_sanitize_json`" exists — **audit disproves this**: no control is registered for slides anywhere, and no `aureon_sanitize_json` function exists in the theme. That is the G6 deferral this plan closes.

No component or animation pipeline changes are allowed — the renderer contract is frozen (per the Stage 10/11 verification gates).

## 2. Decisions (confirmed with user)

1. **Full per-slide model**, schema-first, all fields optional. No more shape migrations.
2. **Minimal additive render:** `hero/slide.php` renders badge / mobile image / overlay **only when set**; with nothing set the output stays byte-identical (guarantees the visual regression gates stay green).
3. **Schema-driven generic repeater control** (`Aureon_Customize_Repeater_Control`) in the theme — the Hero schema is its **first consumer**, registered by the frontend engine via a filter. Reusable later for testimonials/team/FAQ/logo/banner repeaters. No hero-specific logic inside the control.

## 3. Architecture

```
Aureon Customizer                 (theme — generic infra)
  Aureon_Customize_Repeater_Control
    ├─ schema supplied via filter: apply_filters( 'aether_repeater_schemas', ... )
    │     └─ frontend/tokens/tokens.php registers 'hero' schema (first consumer)
    ├─ JS: sortable rows, media picker, visibility eye, add/remove (jQuery UI sortable)
    └─ sanitizer: aetheon 'aureon_sanitize_repeater' (helpers.php, schema keyed)

aureon_settings['aether_hero_slides']   ← existing option bucket (unchanged key)
        ↓ (JSON string OR array — both accepted)
frontend/adapters/adapter-hero.php      ← pass-through for new keys (no logic change)
        ↓
frontend/components/hero/slide.php      ← minimal additive render when keys set
```

Constraints:
- The control must not know how the hero renders.
- `hero/slide.php` must not know anything about the Customizer UI.
- No second settings/storage system. The option key and JSON/array dual-shape contract are preserved.
- **Performance preload hardens** (`aether-performance.php` reads slides raw — see §7).

## 4. Slide schema (v1 contract)

Every key optional; missing keys are normalized away at save time (nothing stored) and fall back to component defaults at render time.

```json
{
  "id": "slide_a81f3c2d",
  "visible": true,
  "headline": "Step into the void",
  "accent": "Void Series",
  "subline": "Precision-cut garments engineered in the dark.",
  "image": "frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg",
  "mobile_image": "",
  "image_alt": "Shadow Drop sneaker on a pedestal",
  "badge": "New drop",
  "overlay": "#00000099",          // hex with alpha, optional; '' = default CSS overlay
  "primary_cta":  { "label": "Shop the collection", "url": "" },
  "secondary_cta": { "label": "Read the story",     "url": "/about/" }
}
```

### Breathing room (reserved, not implemented in v1.2)
- Per-slide animation/preset, alignment, link targets — the schema filter makes adding these later non-migration-breaking (old saved slides still sanitize cleanly).

### Read/backward-compat contract
The legacy defaults in `tokens.php` use the editor shape (`label/title/subtitle/cta/url`). The adapter already normalizes both shapes (adapter-hero.php:30-31). The repeater control writes the new shape; the adapter keeps normalizing both — **no adapter change required for shape reason**.

## 3. Sanitizer spec — `aureon_sanitize_repeater( $input, $setting )` (helpers.php)

Schema-driven: receives `$schema` via `aether_repeater_schemas` filter, keyed by `private $allowed_fields` from the highest-repeated key (the setting ID's `aether_hero_slides` → schema id `hero`).

Rules, **all in the theme's helpers.php** (schema whitelist passed in from frontend):

| Rule | Behavior |
|---|---|
| Whitelist keys | Unknown keys dropped (rejected, not stored) |
| Unwrap | Input may be JSON string or array (stored bucket can be either) |
| Normalize missing optional | Missing optional fields not stored; adapter falls back |
| Text | `sanitize_text_field` on headline/accent/subline/badge/alt |
| URLs | `esc_url_raw` on image/mobile_image/CTA urls (empty allowed) |
| Boolean | `aureon_sanitize_checkbox` on visible (default true) |
| Overlay | `aureon_sanitize_rgba_color` (accepts hex, rgba, '' ) |
| CTA nested | Each CTA: label text-sanitized, url esc_url_raw |
| Malformed nested | Non-array CTA rejected; slide not struct rejected */
| `.repeating keys` | Numeric reindexed even after deletes |
| Stable IDs | Existing `id` **preserved verbatim**. Scopes: if a slide has `id`, keep `slide_xxxxxxxx` (8 hex chars). New slides get `slide_` + `wp_generate_password(8, false)`-style hex. Missing IDs backfilled so reorder can never regenerate. |

ID format: `^slide_[a-f0-9]{8}$`.

**Sanitizer = trust boundary, not display concern.** The adapter still runs its own `sanitize_text_field`/`esc_url_raw` at output — double safety, nothing relies on the sanitizer output alone.

## 4. Control class — `class-repeater-control.php`

Location: `aureon/theme/inc/customizer/controls/`. Style: matches existing `Aureon_Customize_React_Control` (extends `WP_Customize_Control`), `register_control_type` at customizer.php:53-55.

```php
class Aureon_Customize_Repeater_Control extends WP_Customize_Control {
    public $type = 'aureon-repeater';

    // Schema passed via $args['choices']['schema'] → json_encode into $this->json['schema'].
    // Schema keys: id, label, fields[]; each field {key, type, label, placeholder}.
    // Field types v1: 'text', 'textarea', 'url', 'image' (media picker), 'checkbox' (visible),
    // 'color' (overlay, alpha supported), 'cta' (label + url pair).
}
```

Layout: `customizer-controls.js` houses the row renderer (must stay in the theme's existing JS file — no new JS file so the enqueue diff is a no-op; they already load `customizer-controls.js` at helper.php:322). Row DOM: draggable handle · eye (visibility) · collapsible body · remove. Add-repeater footer button.

Order of work within

### Enqueue
`customize_controls_enqueue_scripts` already runs (upsell css, customizer-controls.js). New control adds:
```php
add_action('customize_controls_enqueue_scripts', function(){
  wp_enqueue_script('aureon-repeater', uri.'inc/customizer/controls/js/repeater.js', ['customize-controls','jquery-ui-sortable'], AUREON_VERSION, true);
  wp_enqueue_style('aureon-repeater', uri.'inc/customizer/controls/css/repeater.css', [], AUREON_VERSION);
});
```
Media picker (image fields) via `wp.media` frame — no extra deps.

`->json['value']` = decoded current setting (array). `->link()` wiring standard `WP_Customize_Control`.

## 5. Files

| File | Change |
|---|---|
| `aureon/theme/inc/customizer/controls/class-repeater-control.php` | **NEW** — generic control |
| `aureon/theme/inc/customizer/controls/js/repeater.js` | **NEW** — row renderer, sortable, media frame, visibility |
| `aureon/theme/inc/customizer/controls/css/repeater.css` | **NEW** — admin styles (dark-ready) |
| `aureon/theme/inc/customizer/customizer-helpers.php` | require class-repeater-control.php (controls block, line 16-23) |
| `aureon/theme/inc/customizer/helpers.php` | `aureon_sanitize_repeater()` + schema filter fetch + enqueue hook |
| `aureon/theme/inc/customizer.php` | `register_control_type('Aureon_Customize_Repeater_Control')` at 53-56 |
| `aureon/theme/inc/customizer/fields/frontend.php` | Title "Hero Slides" + field `aureon_settings[aether_hero_slides]` (default from `$defaults`, transport refresh, sanitize `aureon_sanitize_repeater` w/ schema arg) |
| `frontend/tokens/tokens.php` | Register hero schema on `aether_repeater_schemas` filter (first consumer) |
| `frontend/adapters/adapter-hero.php` | Pass-through new keys when present: badge, mobile_image, overlay, alt_text (visible filter) — no shape change |
| `frontend/components/hero/slide.php` | Minimal additive: badge span, `<picture>` mobile swap, overlay inline style — **byte-identical when unset** |
| `aureon/theme/inc/aether-performance.php` | Harden preload: decode JSON before `$slides[0]['image']` | 
| `aureon-doc/STATUS.md` | v1.2.0 row + changelog note (G6 closed) |
| Version bump | `1.1.0 → 1.2.0` (style.css header + AUREON_VERSION + plugin header? — plugin unchanged; theme only) |

**Untouched:** `frontend.css` (100 KB, generated — no edits), swiper/slider init, `hero/slider` component, animations, motion, all other fields files.

## 6. user task — Performance preload hardening (found during audit)

`aether-performance.php:53` reads `$slides[0]['image']` **raw** — when the repeater stores JSON (as the Customizer does), this `['image']` access on a string offset misbehaves and the preload silently dies. Fix: JSON-decode before reading (reuse adapter's own decode pattern), guard with `is_array`.

duplicate line — remove the aether reference.

## 7. Component changes (slide.php)

1. **Badge** — before `<h1>`: `<?php if ( ! empty( $badge ) ) : ?><span class="badge">…`.
2. **Mobile image** — only when BOTH image and mobile_image set: wrap in `picture`:
   `<picture><source media="(max-width: 767px)" srcset="…"><img …></picture>`; else current `<img>` unchanged. `lib` empty → identical markup.
3. **Overlay** — only when overlay set: inline `style="background:{overlay}"` on `.hero-slide-overlay` div; else default class unchanged.

All three guarded so the no-content output is **char-for-char identical** to today. That keeps Stage 10 visual snapshot green without regeneration.

## 8. Verification

1. `php -l` all touched PHP (theme + frontend).
2. `node --check` repeater.js.
3. Live container `aureon_wp`: Customizer → AETHER Frontend → hero panel renders; add/remove/reorder/visibility/media-picker working; save → homepage hero shows the new slides; hide all → default visible state handles.
4. Sanitization: save a JSON payload with junk keys/urls/emoji via REST patch + resave; confirm junk dropped, IDs persist across reorder.
5. Regression: with no edits, homepage hero markup == pre-Sprint markup (diff the hero-slide block).
6. `verify.sh` still passes; no new lint gate trips.

## 7. Rollback

Everything is additive (new control class + JS/CSS + one setting field + guard-only component edits). Rollback = git revert of the 12-file change set + re-deploy theme `frontend/` via `tar.exe -czf` + base64 pipe (container deploy contract).

## Status

- [x] Context audit complete
- [x] Decisions locked (3 answers)
- [x] Plan written
- [ ] Theme control class
- [ ] Repeater JS + CSS
- [ ] Sanitizer (helpers.php)
- [ ] Field registration (frontend.php)
- [ ] Schema registration (frontend/tokens)
- [ ] Adapter pass-through (`adapter-hero.php`)
- [ ] Minimal additive render (`slide.php`)
- [ ] Performance preload hardening
- [ ] STATUS.md + version bump
- [ ] Lint + deploy verify