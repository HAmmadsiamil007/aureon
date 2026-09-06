# FRONTEND-EDITABILITY-AUDIT

| AREA | SAFE FRONTEND-ONLY EDIT | BRIDGE UPDATE REQUIRED | CORE REVIEW REQUIRED | REASON |
|------|------------------------|----------------------|---------------------|--------|
| Hero | YES | NO | NO | Styles isolated in css/ |
| Shop Grid | NO | YES | NO | Requires loop structure |
| Product Detail| NO | NO | YES | Hooks into WooCommerce |
| Footer | YES | NO | NO | Static layout |
