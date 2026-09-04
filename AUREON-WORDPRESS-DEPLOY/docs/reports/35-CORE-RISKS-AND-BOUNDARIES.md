# 35 — CORE RISKS AND BOUNDARIES

## Risk Register

### CRITICAL

| Risk | Impact | Mitigation |
|------|--------|------------|
| WooCommerce core modification | Breaks commerce | FORBIDDEN |
| WordPress core modification | Breaks platform | FORBIDDEN |
| Design resolver corruption | Wrong pack loads | Static cache, validation |
| Security bypass | Data exposure | Nonce verification, CSP |

### HIGH

| Risk | Impact | Mitigation |
|------|--------|------------|
| FermPageData structure change | Client JS breaks | Schema validation |
| Cart AJAX modification | Cart breaks | Nonce + validation |
| Template routing change | Wrong template loads | Priority chain, testing |
| Asset loading change | Wrong CSS/JS loads | Active-pack-only proof |

### MEDIUM

| Risk | Impact | Mitigation |
|------|--------|------------|
| Customizer setting change | Visual regression | Token system, defaults |
| Adapter output change | Component breaks | ViewModel normalization |
| Hook removal | Feature breaks | Dependency tracing |
| Demo content change | Visual gap | Fallback system |

### LOW

| Risk | Impact | Mitigation |
|------|--------|------------|
| Component template change | Visual change | Pack override mechanism |
| Token default change | Visual change | Customizer wins |
| Performance regression | Slower load | Active-pack-only |

## Boundary Rules

### Core ↔ Client Pack
- Core provides data via bridge
- Client pack provides presentation
- No direct modification across boundary

### Core ↔ WooCommerce
- Core uses WC APIs via adapters
- No WC core modification
- WC templates used for checkout/account

### Core ↔ Customizer
- Core reads settings via aureon_get_option()
- Token system bridges settings to CSS
- Bridge JS bridges settings to client

### Client Pack ↔ Bridge
- Bridge provides data in client format
- Client pack reads data from globals
- No business logic in client pack

## Known Limitations

1. Complete-page HTML is frozen (manual update required)
2. Checkout uses WC native template (not frozen)
3. Account logged-in uses WC native template
4. Single resolver per request (no multi-design runtime)
5. Demo content gated by master switch
6. CSP in report-only mode by default
