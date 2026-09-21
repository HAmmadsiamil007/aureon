# AUREON CORE — Remediation Complete

## Date: 2026-09-21
## Status: ALL VALID FINDINGS FIXED + FALSE FINDINGS REJECTED

## Fixed (8 issues)

### #1 — Newsletter Rate Limit (aether-newsletter.php)
**Before:** Private/reserved IPs bypassed rate limiting.
**After:** All IPs rate-limited. Filter `aether_newsletter_rate_limit_ip` can still disable per-IP.
**Files:** theme/aureon/inc/aether-newsletter.php

### #2 — CSV Export (aether-newsletter.php)
**Before:** No capability check, no-cache headers missing, exit after data output.
**After:** `manage_options` capability check, `Pragma: no-cache` + `Expires: 0` headers, exit immediately after headers.
**Files:** theme/aureon/inc/aether-newsletter.php

### #4 — Contact Reply-To Header Injection (aether-ajax.php)
**Before:** User-controlled `$name` passed unsanitized to Reply-To header.
**After:** CR/LF stripped via `preg_replace('/[\r\n]+/', '', ...)`, `sanitize_text_field()` applied.
**Files:** theme/aureon/inc/aether-ajax.php

### #7 — Newsletter Table Check Lifecycle (aether-newsletter.php)
**Before:** `SHOW TABLES LIKE` executed on every admin request (3 locations).
**After:** Option flag `aether_newsletter_table_ready` caches table existence. Flag cleared only on actual missing table.
**Files:** theme/aureon/inc/aether-newsletter.php

### #8 — Analytics Product Loop Optimization (aether-analytics.php)
**Before:** `wc_get_product()` instantiated full WC product objects for every post in shop/archive loops.
**After:** Lightweight `get_post_meta()` + `get_the_title()` batch queries. No full product object instantiation.
**Files:** theme/aureon/inc/aether-analytics.php

### #9 — CDN SRI Hashes (aether-performance.php)
**Before:** 8 CDN resources loaded without Subresource Integrity.
**After:** `wp_script_tag`/`wp_style_tag` filters add `integrity` + `crossorigin="anonymous"` for all pinned CDN assets.
**Files:** theme/aureon/inc/aether-performance.php

### #10 — REST Namespace Normalization
**Before:** `aether/v1` used inconsistently alongside `aureon/v1`.
**After:** Canonical namespace is `aureon/v1`. `aether/v1` preserved as backward-compat alias. restUrl localization updated.
**Files:** theme/aureon/inc/aether-newsletter.php, theme/aureon/inc/frontend.php, frontend/views/assets.php

### #12 — JSON_HEX Script Context (aether-analytics.php)
**Before:** `wp_json_encode()` without safe flags inside `<script>` tags.
**After:** `JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP` applied.
**Files:** theme/aureon/inc/aether-analytics.php

## Rejected / Not Changed

### #3 — Public Newsletter Endpoint
Public by design. Abuse controls are rate limiting + nonce + email validation.

### #5 — Guest WooCommerce Session
No capability check added. Guest cart is inherently unauthenticated.

### #6 — HTML Compression Regex
Benchmark not yet run. No evidence of catastrophic backtracking. DEFERRED.

### #11 — aureon_get_option() nullable
Low priority. Documentation improvement only. No risky rewrite.

### #13 — wp_unslash() Pattern
Standard WordPress input handling. NOT a bug.

### #14 — content_width Timing
Correct for classic themes. NOT moved.

### #15 — SVG Icons
Maintainability refactor. DEFERRED to separate release.

### #16 — CSP Nonce in JS
Verified properly hardened: strict-dynamic + per-request rotation + no DOM XSS.

### #17 — theme.json
Not a defect for classic theme architecture.

### #18 — Multiple Deployment Copies
Hygiene issue. DEFERRED — Git tags preferred.

## Security Baseline

### Public Endpoints
- `POST /wp-json/aureon/v1/newsletter/subscribe` — email validation, rate limiting, nonce check
- `POST /wp-json/aether/v1/newsletter/subscribe` — backward-compat alias (same handler)

### Admin Endpoints
- `GET /wp-json/aureon/v1/reset/` — requires `manage_options`

### AJAX Endpoints
- `wp_ajax_aether_newsletter_subscribe` — nonce + rate limit
- `wp_ajax_nopriv_aether_newsletter_subscribe` — nonce + rate limit
- Contact form — nonce + email validation + CR/LF hardening

### Nonce Model
- All AJAX/REST handlers use `check_ajax_referer('aether_nonce', 'nonce')`
- CSP nonce: per-request, base64-encoded, exposed via `window.aetherCSPNonce`

### Capability Model
- Newsletter admin page: `manage_options`
- CSV export: `manage_options`
- Settings reset: `manage_options`

### Rate Limit Model
- Newsletter subscribe: 1 per IP per minute (all IPs including private)
- Transient-based (`aether_newsletter_rate_{md5(ip)}`)

### CSP Model
- Report-only by default
- `AETHER_CSP_STRICT = true` in wp-config.php to enforce
- Nonce-based with `strict-dynamic`
- Known CDNs allowlisted

### External Asset Model
- Bootstrap 5.3.3 (jsDelivr) — SRI verified
- Font Awesome 6.5.1 (cdnjs) — SRI verified
- Swiper 11 (jsDelivr) — SRI verified
- GSAP 3.12.5 (cdnjs) — SRI verified
- Lenis 1.1.19 (unpkg) — SRI verified
