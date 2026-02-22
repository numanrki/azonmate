# Changelog

All notable changes to AzonMate will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.3] - 2026-02-23

### Added
- **AJAX tab switching** on Settings page — tabs now switch instantly via JavaScript with no page reload. Hash-based URL state (`#api`, `#display`, etc.) so bookmarks and browser history work correctly.
- **Tab state preservation** after form save — `initTabFormPreserve()` injects the active tab hash into `_wp_http_referer` so WordPress redirects back to the correct tab after saving options.
- **Page Hero headers** on all 4 admin pages (Settings, Products, Showcase Builder, Analytics) — consistent gradient icon circle, title, and subtitle with responsive layout.
- **Modern pill-style tab navigation** — white card container with rounded corners, gradient orange active state, dashicon support, smooth hover transitions.
- **Card-wrapped form sections** — settings forms now rendered inside rounded cards with subtle shadows, improved input field styling, and branded orange gradient submit buttons.
- **Enhanced test connection & cache clear panels** — wrapped in styled card containers.

### Changed
- Settings page no longer uses server-side `$active_tab` conditional rendering; all 6 tab sections are always rendered and shown/hidden via CSS + JS.
- Tabs array now includes icon metadata for each tab.
- Removed old underline-style tab CSS; replaced with pill/chip-style design.
- Version bumped to 1.3.3.

## [1.3.2] - 2026-02-23

### Added
- **Admin brand bar** on every plugin page — dark header with AzonMate logo, version badge, "by Numan" author credit, GitHub repo link, and "Star on GitHub" CTA button.
- **Admin footer bar** on every plugin page — version, links to GitHub repo, bug reports, and releases.
- Shared partial `partials/admin-bar.php` renders both bars; included in Settings, Analytics, Products, and Showcase Builder pages.
- Full responsive CSS for both bars with gradient star button, pill version tag, and hover effects.

### Changed
- Version bumped to 1.3.2.

## [1.3.1] - 2026-02-22

### Added
- **Amazon Affiliate Disclosure** — "As an Amazon Associate, I earn from qualifying purchases." shown once per showcase block, inside the product cards container.
- New **Display Setting** toggle to enable/disable the disclosure (enabled by default, as required by the Amazon Associates program).
- `azon_mate_render_disclosure()` helper function in template-functions.php.
- Disclosure integrated into all 8 showcase templates: grid, list, masonry, table, hero, compact, split, deal.

### Changed
- **Analytics Dashboard completely redesigned** — modern card-based UI with gradient stat cards, visual bar chart, progress-bar performance indicators, rank badges, and responsive two-column layout replaces the old WordPress `widefat` tables.
- Added two new stats to analytics: **Daily Average** and **Peak Day Clicks**.
- Version bumped to 1.3.1.

## [1.3.0] - 2026-02-22

### Added
- 4 new **single-product showcase layouts**: Hero Card, Compact, Split, Deal Card.
- **Visual Showcase Builder** — point-and-click shortcode generator with live WYSIWYG preview. Admin preview now uses the exact same CSS as the frontend.
- **Manual Products** CRUD system — add products without an API connection.
- Fully scoped showcase CSS — every rule namespaced under `.azonmate-showcase` to eliminate theme/plugin conflicts.
- Theme reset block inside the showcase container (neutralises h2, h3, a, ul, table, img defaults).

### Fixed
- **Critical:** Buy-button class mismatch (`azonmate-buy-button` → `azonmate-buy-btn`) that caused all CTA buttons to receive zero styling on the frontend.
- Missing `$showcase_builder` property declaration in the Plugin class (PHP 8.2 deprecation warning).
- Inconsistent responsive breakpoints across layouts — now standardised at 960 px (tablet) and 600 px (mobile).

### Changed
- Showcase Builder admin page now loads `azonmate-public.css` + `azonmate-showcase.css` for true WYSIWYG rendering.
- Template renderer `valid_layouts` array expanded to include: hero, compact, split, deal.
- Version bumped to 1.3.0 across all version locations.

## [1.0.0] - 2026-01-01

### Added
- Initial release of AzonMate – Amazon Affiliate Product Engine.
- **Amazon PA-API 5.0** integration with AWS Signature v4 authentication.
- Support for **10 Amazon marketplaces**: US, UK, DE, FR, IN, CA, JP, IT, ES, AU.
- **Product Box** shortcode (`[azonmate box="ASIN"]`) with three templates: default, horizontal, compact.
- **Text Link** shortcode (`[azonmate link="ASIN"]text[/azonmate]`).
- **Image Link** shortcode (`[azonmate image="ASIN"]`).
- **Field** shortcode (`[azonmate field="price" asin="ASIN"]`) for individual data points.
- **Product List** shortcode (`[azonmate list="..."]`) with vertical and grid layouts.
- **Comparison Table** shortcode (`[azonmate table="..."]`) with configurable columns and highlight.
- **Bestseller** shortcode (`[azonmate bestseller="Category"]`).
- **New Releases** shortcode (`[azonmate new_releases="Category"]`).
- **Gutenberg blocks** for Product Box, Product List, Comparison Table, Bestseller, and Text Link.
- Editor **product search modal** (Classic Editor / TinyMCE integration).
- **Smart caching** with database storage and WordPress transients.
- **WP-Cron** scheduled cache refresh and click data cleanup.
- **Geo-targeting** with CloudFlare header detection and ip-api.com fallback.
- Automatic **affiliate tag swapping** based on visitor country.
- **Click tracking** via AJAX with privacy-safe IP hashing (SHA-256 with daily salt).
- **Analytics dashboard** with click stats, top products, top posts, and CSV export.
- **WordPress dashboard widget** for quick click statistics.
- **Template override system** (theme → plugin fallback).
- Fully **responsive CSS** with CSS Custom Properties for easy theming.
- **Dark mode** support via `prefers-color-scheme`.
- **BEM naming** convention throughout all CSS.
- **AES-256-CBC encryption** for API key storage.
- AJAX endpoints secured with nonces and capability checks.
- Rate limiting on API search requests (5 per 10 seconds).
- Admin settings page with six tabbed sections.
- **Test Connection** button for API credential validation.
- Clean uninstall with optional data deletion.
