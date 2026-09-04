# FEATURE-LOSS-AUDIT

| FEATURE | EXPECTED | ACTUAL STATUS | EVIDENCE | AFFECTED FILES | ROOT CAUSE |
|---------|----------|---------------|----------|----------------|------------|
| Homepage hero | Dynamic content | PARTIAL | adapter-hero reads data, template is static | aureon/frontend/adapters/adapter-hero.php | Bridge data not injected |
| Featured categories | WooCommerce tags | PARTIAL | Shows hardcoded | section-categories.php | Missing dynamic mapping |
| Shop/product archive | Product loop | VERIFIED_WORKING | Loop works | views/shop.php | None |
| Product detail page | All details | BROKEN | console-errors.txt mentions JS crash on gallery | views/product.php | JS initialization order |
| Cart | Badge updates | BROKEN | console-errors.txt | header.php | WooCommerce AJAX not bound |
| Checkout | Payment processing | WORKING_NEEDS_HARDENING | Works but styles broken | checkout/form.php | CSS missing |
| My Account | Login | VERIFIED_WORKING | Functional | myaccount/login.php | None |
