# DIRECTORY STRUCTURE

**Status:** PERMANENT REFERENCE
**Date:** 2026-08-31

---

## Authoritative Locations

```
C:\Users\hamma\Downloads\phantom\wordpress\
│
├── aureon/                          🔒 GOLDEN CORE
│   ├── core engine                  🔒 PROTECTED
│   ├── theme/                       🔒 PROTECTED
│   ├── plugin/                      🔒 PROTECTED
│   ├── ferm-page.php                🔒 PROTECTED
│   └── frontend/
│       ├── adapters/                🔒 PROTECTED
│       ├── sections/                🔒 PROTECTED
│       ├── views/                   🔒 PROTECTED
│       ├── tokens/                  🔒 PROTECTED
│       └── designs/
│           └── fermliving/          ✏️ CLIENT PACK (changeable)
│               ├── manifest.json
│               ├── tokens.php
│               ├── composer.php
│               ├── index.html
│               ├── products/
│               ├── collections/
│               ├── pages/
│               ├── blogs/
│               ├── css/
│               ├── js/
│               ├── cdn/
│               ├── demo/            ✏️ DEMO DATA
│               ├── mapper/
│               └── data/
│
├── docs/                            📚 DOCUMENTATION
│   ├── architecture/                📚 Architecture docs
│   │   ├── GOLDEN-AUREON-FRONTEND-WORKFLOWS.md
│   │   ├── DEMO-REFERENCE-CONTENT-SYSTEM.md
│   │   ├── DEMO-REFERENCE-SYSTEM-IMPLEMENTATION-PLAN.md
│   │   ├── NEW-CLIENT-TEMPLATE-CREATION-PLAN.md
│   │   ├── FRONTEND-REPLACEMENT-PLAN.md
│   │   ├── FRONTEND-EDIT-PLAN.md
│   │   └── DIRECTORY-STRUCTURE.md
│   └── reports/                     📚 REPORTS (secondary copy)
│       └── *.md
│
├── reports/                         📚 REPORTS (authoritative)
│   ├── 00-CORE-REPORT-INDEX.md
│   ├── 01-CORE-EXECUTIVE-SUMMARY.md
│   ├── ...
│   └── 38-DEMO-REFERENCE-SYSTEM-CONTRACT.md
│
├── docker-compose.yml               🐳 Docker setup
├── Dockerfile                       🐳 Docker build
└── .env                             🔐 Environment vars
```

---

## Key Distinctions

### Golden Core (PROTECTED)

```
aureon/core engine     🔒 DO NOT MODIFY
aureon/theme/          🔒 DO NOT MODIFY
aureon/plugin/         🔒 DO NOT MODIFY
aureon/ferm-page.php   🔒 DO NOT MODIFY
aureon/frontend/adapters/   🔒 DO NOT MODIFY
aureon/frontend/sections/   🔒 DO NOT MODIFY
aureon/frontend/views/      🔒 DO NOT MODIFY
aureon/frontend/tokens/     🔒 DO NOT MODIFY
```

### Client Pack (CHANGEABLE)

```
aureon/frontend/designs/fermliving/  ✏️ SAFE TO MODIFY
├── manifest.json        ✏️
├── tokens.php           ✏️
├── composer.php         ✏️
├── index.html           ✏️
├── products/            ✏️
├── collections/         ✏️
├── pages/               ✏️
├── blogs/               ✏️
├── css/                 ✏️
├── js/                  ✏️
├── cdn/                 ✏️
├── demo/                ✏️ DEMO DATA
├── mapper/              ✏️
└── data/                ✏️
```

### Documentation

```
docs/architecture/       📚 Operational docs
docs/reports/            📚 Secondary report copy
reports/                 📚 Authoritative reports
```

---

## Why This Matters

### For Git

```
aureon/ changed
  → Could mean Golden Core modified (BAD)
  → Could mean client pack modified (OK)
  → CHECK which subdirectory changed
```

### For CLI/AI Agents

```
DO NOT assume aureon/ change = Core change
ALWAYS check: aureon/frontend/designs/ = client pack
ALWAYS check: aureon/theme/ = Core (protected)
```

### For Future Clients

```
Client B pack:
  aureon/frontend/designs/clientb/  ✏️

Client C pack:
  aureon/frontend/designs/clientc/  ✏️

Golden Core:
  aureon/theme/  🔒 (shared by all clients)
```

---

## Report Locations

### Authoritative

```
reports/
  00-CORE-REPORT-INDEX.md
  01-CORE-EXECUTIVE-SUMMARY.md
  ...
  38-DEMO-REFERENCE-SYSTEM-CONTRACT.md
```

### Secondary (copy)

```
docs/reports/
  (same files as reports/)
```

### Why Two Locations

```
reports/           → Core forensic reports (authoritative)
docs/reports/      → Documentation layer (secondary)
docs/architecture/ → Operational architecture docs
```

---

## Path Reference

| Component | Path | Status |
|-----------|------|--------|
| Golden Core | `aureon/` | 🔒 PROTECTED |
| Theme | `aureon/theme/` | 🔒 PROTECTED |
| Plugin | `aureon/plugin/` | 🔒 PROTECTED |
| Frontend Engine | `aureon/frontend/` | 🔒 PROTECTED |
| Client Packs | `aureon/frontend/designs/` | ✏️ CHANGEABLE |
| Ferm Pack | `aureon/frontend/designs/fermliving/` | ✏️ CHANGEABLE |
| Demo Data | `aureon/frontend/designs/fermliving/demo/` | ✏️ CHANGEABLE |
| Reports | `reports/` | 📚 AUTHORITATIVE |
| Architecture Docs | `docs/architecture/` | 📚 OPERATIONAL |
