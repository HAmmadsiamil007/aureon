# AUREON CORE STATE

## Architecture Status
- AUREON CORE = FROZEN
- CODE REVIEW HARDENING = COMPLETE (2026-09-21)
- SECURITY BASELINE = VERIFIED
- DESIGN LAYER = ONLY NORMAL CLIENT EDIT SURFACE

## Security Baseline
- eval()/exec()/system()/shell_exec() = 0
- CRLF injection = BLOCKED
- Newsletter rate limiting = ALL IPs (1/min)
- REST = aureon/v1 canonical + aether/v1 backward compat
- AJAX nonces = enforced
- Capability checks = manage_options on admin endpoints
- JSON_HEX = applied to script output
- CDN SRI = 8 resources verified

## Core/Design Boundary
- theme/aureon/ = FROZEN CORE
- frontend/designs/ = CLIENT DESIGN LAYER
- plugins/aureon-studio/ = FROZEN CORE
- mu-plugins/ = FROZEN INFRASTRUCTURE

## Future Redesign Rule
CORE FILES MODIFIED: 0
DESIGN FILES MODIFIED: X
DYNAMIC CONTRACTS MODIFIED: 0
REGRESSION TESTS: PASS

Any deviation from "CORE FILES MODIFIED: 0" for a client redesign requires explicit justification.
