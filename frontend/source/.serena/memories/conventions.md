# phantom-frontend conventions

- HTML: semantic elements (header, nav, main, section, article, footer)
- CSS: SCSS files compiled to CSS
- JS: vanilla, no framework
- PHP (parent dir): WordPress plugin conventions
  - Class-based architecture with `PHANTOM_` prefix
  - PSR-4-ish autoloading via plugin bootstrap
  - `includes/` for classes, `admin/` for admin UI
  - `components/` for reusable UI blocks
  - PHP namespaces: `PHANTOM\`
  - snake_case for DB options, CamelCase for class names
