# ARCHITECTURE-PROBLEM-REPORT

| # | SEVERITY | PROBLEM | ROOT CAUSE | IMPACT | AFFECTED FILES | AFFECTED FEATURE | RECOMMENDED LAYER | SAFE FIX | DO NOT TOUCH | TEST REQUIRED |
|---|----------|---------|------------|--------|----------------|------------------|-------------------|----------|--------------|---------------|
| 1 | P0-CRITICAL | ferm-page.php version discrepancy | Duplicate files (34987 vs 25062 bytes) | Unpredictable behavior | aureon/ferm-page.php, theme/aureon/ferm-page.php | Core framework | Golden Core | Use aureon/ | theme/ | YES |
| 2 | P1-HIGH | Core/bridge boundary violation | Adapters writing HTML | Hard to edit | adapters/adapter-hero.php | Hero | Bridge | Remove HTML | ferm-page.php| YES |
| 3 | P2-MEDIUM | Hardcoded demo data | Missing WP queries | Static content | section-categories.php | Categories | Frontend | Fetch WP data | Core | NO |
