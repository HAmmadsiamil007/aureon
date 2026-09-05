# 23 — SECURITY ARCHITECTURE

## Security Headers

| Header | Value | Source |
|--------|-------|--------|
| X-Content-Type-Options | nosniff | aether-security.php |
| X-Frame-Options | SAMEORIGIN | aether-security.php (except Customizer) |
| Referrer-Policy | strict-origin-when-cross-origin | aether-security.php |
| Permissions-Policy | camera=(), microphone=(), geolocation=(), interest-cohort=() | aether-security.php |
| Content-Security-Policy | report-only (or strict) | aether-security.php |
| Strict-Transport-Security | max-age=31536000; includeSubDomains; preload | aether-security.php (HTTPS only) |
| X-Powered-By | Removed | aether-security.php |

## CSP Policy

```
default-src 'self'
script-src 'self' 'nonce-{random}' 'strict-dynamic' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com
style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com
img-src 'self' data: https: blob:
font-src 'self' data: https://cdnjs.cloudflare.com
connect-src 'self' https://www.google-analytics.com
frame-src 'self' https://www.youtube.com https://player.vimeo.com
object-src 'none'
base-uri 'self'
form-action 'self'
frame-ancestors 'self'
```

**Nonce-based** for inline scripts. Report-only by default; enforce via `AETHER_CSP_STRICT` constant.

## Nonce System

- `aether_get_csp_nonce()` — regenerated per request
- Added to all `aether-*` and `aureon-*` script tags
- Exposed to JS via `window.aetherCSPNonce`

## AJAX Security

| Endpoint | Nonce | Validation | Rate Limit |
|----------|-------|------------|------------|
| ferm_cart_add | ferm_cart_nonce | absint | — |
| ferm_cart_update | ferm_cart_nonce | sanitize | — |
| ferm_cart_get | ferm_cart_nonce | — | — |
| aether_wishlist_toggle | aether_nonce | absint | — |
| aether_quick_view | aether_nonce | absint | — |
| aether_contact_submit | aether_contact | sanitize_email, sanitize_textarea_field | 1/min/IP |

## Data Exposure

| Data | Public? | Notes |
|------|---------|-------|
| Site name/tagline | ✅ Yes | bloginfo() |
| Product data | ✅ Yes | Public catalog |
| Cart data | ❌ No | Session-based |
| Customer data | ⚠️ Auth only | Own data only |
| Nonce | ⚠️ Per-request | Required for AJAX |
| Admin URLs | ❌ No | Not in client data |

## Input Sanitization

- `absint()` for IDs
- `sanitize_text_field()` for text
- `sanitize_email()` for emails
- `sanitize_textarea_field()` for messages
- `esc_attr()` for HTML attributes
- `esc_url_raw()` for URLs
- `wp_json_encode()` for JSON output
