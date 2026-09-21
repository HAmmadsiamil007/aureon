# AUREON CORE — Forensic Triage of OCR Review Findings

## Date: 2026-09-21
## Status: APPROVED BY SENIOR ARCHITECT

## Classification Matrix

| # | Finding | Verdict | Action |
|---|---------|---------|--------|
| 1 | Newsletter private-IP rate-limit bypass | VALID | FIX |
| 2 | CSV export header/exit handling | PARTLY VALID | REFACTOR (not as originally reported) |
| 3 | Public newsletter REST endpoint | NOT A VULNERABILITY | KEEP PUBLIC — add abuse controls |
| 4 | Contact Reply-To injection | VALID HARDENING | FIX — strip CR/LF + test |
| 5 | Guest WooCommerce session init | OVERSTATED | DO NOT ADD CAPABILITY CHECK |
| 6 | "Catastrophic" HTML regex | UNPROVEN | BENCHMARK FIRST — replace only if evidence |
| 7 | Newsletter table check every admin | VALID OPTIMIZATION | FIX lifecycle |
| 8 | wc_get_product() analytics loop | POTENTIALLY VALID | PROFILE BEFORE/AFTER |
| 9 | CDN resources without SRI | VALID HARDENING | ADD SRI or self-host |
| 10 | aureon/v1 vs aether/v1 namespace | VALID CONSISTENCY | NORMALIZE WITH COMPAT |
| 11 | aureon_get_option() nullable | LOW PRIORITY | DOCS ONLY — no risky rewrite |
| 12 | wp_json_encode() in script context | VALID | FIX with JSON_HEX flags |
| 13 | "Double wp_unslash" | INCORRECT FINDING | DO NOT CHANGE |
| 14 | content_width timing | INCORRECT/OVERSTATED | DO NOT MOVE |
| 15 | Hardcoded SVG strings | MAINTAINABILITY | DEFERRED to separate refactor |
| 16 | CSP nonce exposed to JS | HARDENING | REVIEW — minimize exposure |
| 17 | Missing theme.json | NOT A DEFECT | DO NOT ADD during hardening |
| 18 | Multiple deployment copies | HYGIENE | DEFERRED — Git tags preferred |

## Explicitly Rejected Findings

### #13 — wp_unslash() pattern is NORMAL WordPress input handling
`sanitize_text_field( wp_unslash( $_POST[...] ) )` is standard WordPress practice.
wp_unslash() is NOT redundant. DO NOT REMOVE.

### #14 — content_width timing is CORRECT for classic themes
WordPress classic-theme docs show $content_width in functions.php attached to after_setup_theme.
DO NOT MOVE.

### #3 — Public newsletter endpoint MUST remain public
Public subscription is intentional. Problem is ABUSE PREVENTION not authentication.

### #5 — Guest cart MUST remain unauthenticated
wp_ajax_nopriv_woocommerce_add_to_cart is required for guest carts.
Capability check would break guest functionality.

## Correct Workflow

```
CURRENT AUREON CORE
    ↓
Full-file OCR scan
    ↓
Finding validation against CURRENT code
    ↓
Valid fixes only
    ↓
Performance benchmarks (for #6, #8)
    ↓
Minimal fixes
    ↓
OCR review of the patch
    ↓
WordPress/WooCommerce runtime regression
    ↓
Final release gate
```

## Design-Layer Firewall

NEVER modify:
- frontend/designs/vineta/
- Client visual HTML/CSS/JS
- Client visual assets
- Page design
- Product-card design
- Header/footer appearance

## Files Changed in This Session

### aether-ajax.php — FIX #4 (Contact header injection)
- Added CR/LF stripping on $name
- Added sanitize_text_field() on Reply-To header construction

### aether-newsletter.php — FIX #1 (Rate limit) + FIX #2 (CSV export)
- Rate limit now applies to ALL IPs (removed private-IP bypass)
- CSV export: added capability check, header caching, immediate exit
- CSV export: proper no-cache headers

### aether-newsletter.php — FIX #7 (Table check optimization) — PENDING
### aether-analytics.php — FIX #8 (Product loop optimization) — PENDING
### aether-performance.php — FIX #6 (HTML regex benchmark) — PENDING
### frontend.php — FIX #9 (CDN SRI hashes) — PENDING
### class-rest.php — FIX #10 (Namespace normalization) — PENDING
### aether-analytics.php — FIX #12 (JSON_HEX in script) — PENDING
