# CDG Core - Must-Use Plugin

WordPress optimizations, security hardening, and agency features for Crawford Design Group client sites.

## Version 1.0.0

### Installation

1. Upload `cdg-core/` folder to `/wp-content/mu-plugins/`
2. Upload `cdg-core.php` to `/wp-content/mu-plugins/`
3. Visit **Settings → CDG Core** to configure

### Features

- WordPress head cleanup & emoji removal
- Security hardening (XML-RPC, uploads, headers)
- Performance optimizations (Gutenberg, queries, images)
- Gravity Forms / Divi compatibility fixes
- Documentation system for editors
- CPT Dashboard widgets
- Post type renaming
- Admin branding & custom CSS

### Settings

Visit **Settings → CDG Core** to configure all features.

### Post Revisions

Add to `wp-config.php`:

```php
define('WP_POST_REVISIONS', 5);
```
