# Plugin Bridge Matrix (Phase 8)

Every third-party plugin is accessed exclusively through a
`Lumina\Core\Bridges\*` capability adapter (ADR-007). Bridges detect
presence/version, expose a capability surface, and never call vendor symbols
unguarded. When a plugin is absent the bridge reports inactive and its
methods return safe defaults — Lumina never throws.

Source of truth: `app/Bridges/config/plugins.php`.

| Bridge slug   | Plugin                 | Capabilities                               |
| ------------- | ---------------------- | ------------------------------------------ |
| `acf`         | Advanced Custom Fields | fields, sub_fields, image, group, repeater |
| `rankmath`    | Rank Math SEO          | meta_title, meta_description, robots       |
| `yoast`       | Yoast SEO              | meta_title, meta_description, canonical    |
| `wpml`        | WPML                   | locale, languages, is_translated           |
| `polylang`    | Polylang               | locale, languages, is_translated           |
| `fluentforms` | Fluent Forms           | embed                                      |
| `gravity`     | Gravity Forms          | embed, enqueue_assets                      |
| `wpforms`     | WPForms                | embed                                      |
| `buddypress`  | BuddyPress             | avatar, profile_url                        |
| `bbpress`     | bbPress                | is_topic, is_reply, forum_url              |
| `learndash`   | LearnDash              | course_id, enrollment_status               |
| `tec`         | The Events Calendar    | events, ticket_count                       |
