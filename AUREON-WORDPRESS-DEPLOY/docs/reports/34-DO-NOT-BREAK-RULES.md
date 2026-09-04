# 34 — DO-NOT-BREAK RULES

## Golden Rules

### 1. GOLDEN CORE IS PROTECTED
The Golden AUREON core is a release baseline. Client frontend work happens inside a controlled client-pack boundary.

### 2. CLIENT FRONTEND IS PRESENTATION
The client frontend owns HTML, CSS, JS, animations, responsive presentation, visual design.

### 3. BRIDGE IS CONNECTOR
The bridge owns data mapping, URL rewriting, client-state translation, business-action translation.

## Absolute Prohibitions

### NEVER
- Rebuild a complete client frontend into AUREON sections
- Modify WooCommerce core for presentation convenience
- Destroy working platform capabilities because a client doesn't use them
- Load all client assets and hide them with CSS
- Leak client state between designs
- Remove required hooks without dependency tracing
- Change business logic to compensate for a visual issue
- Convert complete HTML/CSS/JS into AUREON components unless explicitly choosing Component Mode

### ALWAYS
- Edit the client pack, not the Golden Core
- Preserve platform contracts when editing client frontend
- Trace dependencies before removing hooks
- Verify isolation when switching designs
- Maintain rollback capability
- Document architectural decisions

## Change Classification

### SAFE ✅
- Add new adapter
- Add new component
- Add new section
- Add new design pack
- Override pack templates
- Modify client pack HTML/CSS/JS
- Add filter on existing hook

### REVIEW REQUIRED ⚠️
- Modify template routing
- Modify asset loading
- Add Customizer setting
- Modify adapter output
- Modify security logic
- Modify design resolver behavior

### HIGH RISK 🔴
- Modify manifest schema
- Modify FermPageData structure
- Modify cart/checkout flow
- Modify authentication flow

### FORBIDDEN ❌
- Modify WooCommerce core files
- Modify WordPress core files
- Rebuild client frontend as components
- Delete required hooks
- Remove platform capabilities
- Change business logic for visual reasons
- Load inactive client assets
- Leak client state

## Regression References

Permanent test references that must never break:
- Product #834 (simple product)
- Product #828 (variable product)
- Account login/logout
- Cart add/update/remove
- Checkout flow
- Search functionality
- Menu navigation
- Customizer round-trip
