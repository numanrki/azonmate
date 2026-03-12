# Changelog

All notable changes to AzonMate will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.1] - 2026-03-12

### Improved
- Update notification banner now displays on all WordPress admin pages (including Dashboard), not just AzonMate screens.
- readme.txt and README.md updated to document one-click updates and dashboard notification features.

## [2.3.0] - 2026-03-12

### Added
- Updates tab in Settings page — check for new releases and install updates with one click directly from the plugin.
- Admin notification banner on all AzonMate pages when a newer version is available, with "View Update" link.
- Live install button — triggers WordPress's built-in Plugin_Upgrader via AJAX, downloads and applies the latest GitHub release without leaving the page.
- Spinning icon animation on "Check for Updates" button while fetching.

## [2.2.2] - 2026-03-12

### Added
- GitHub-based auto-updater — plugin checks for new releases and updates via WordPress's native update system.

### Improved
- Version constant (`AZON_MATE_VERSION`) now derived from plugin header via `get_file_data()` — only one line to edit for version bumps.

## [2.2.1] - 2026-03-12

### Added
- Buy Me a Coffee donation button in admin header bar — sits next to Star on GitHub with matching style.

## [2.2.0] - 2026-03-12

### Added
- 12 new Amazon marketplaces: MX (Mexico), BR (Brazil), NL (Netherlands), BE (Belgium), PL (Poland), SE (Sweden), TR (Turkey), SA (Saudi Arabia), AE (UAE), EG (Egypt), IE (Ireland), SG (Singapore) — total: 22 marketplaces across NA, EU, and FE regions.
- Country-to-marketplace mappings for all new locales in geo-targeting.

### Fixed
- Shortcode `fallback_output()` built broken URLs — used marketplace code (e.g. `US`) as domain instead of resolving via `Marketplace::get_domain()` (e.g. `amazon.com`). Default also changed from invalid `'www'` to `'US'`.

### Changed
- `LinkRewriter` no longer maintains a hardcoded domain map — now reads from the central `Marketplace` class, so new marketplaces are automatically supported.

## [2.1.3] - 2026-03-11

### Fixed
- Product card button icons (Edit, Fetch, Delete, Copy Shortcode) misaligned with text — replaced broken inline `vertical-align` with flexbox `inline-flex` + `align-items: center` for consistent icon–text alignment.

## [2.1.2] - 2026-03-11

### Changed
- Plugin distribution size reduced from ~4 MB to ~2.5 MB (35% smaller).
- Removed `lib/creatorsapi-sdk/` directory — fully duplicated in `vendor/amazon/creatorsapi-php-sdk/` (saved ~1.2 MB).
- Stripped vendor documentation files (CHANGELOG.md, README.md, UPGRADING.md, package-lock.json) from all dependency packages (saved ~220 KB).
- Removed dev-only root files from distribution: `package.json`, `webpack.config.js`, `composer.lock` (saved ~25 KB).
- Removed Composer path repository reference from `composer.json` (no longer needed since `lib/` is deleted).

## [2.1.1] - 2026-03-11

### Fixed
- Critical error on deployment — `vendor/` directory (SDK + Guzzle dependencies) was excluded from distribution by `.gitignore`, causing `Class not found` fatal error on production.
- Composer path repository used symlink instead of file copy (`"symlink": false` added), breaking SDK autoloading on servers that don't preserve symlinks.

## [2.1.0] - 2025-07-15

### Added
- **Official Amazon Creators API PHP SDK** (v1.2.0) — replaces the custom HTTP/OAuth implementation with Amazon's official typed SDK classes (`DefaultApi`, `Configuration`, typed request/response models).
- **Composer Dependency Management** — Guzzle HTTP 7.x, PSR-7, PSR-HTTP-Client, and the SDK itself installed via Composer autoloader (`vendor/autoload.php`).

### Changed
- `Amazon_API` class fully rewritten to use SDK `DefaultApi` client, `SearchItemsRequestContent`, `GetItemsRequestContent`, `GetBrowseNodesRequestContent` typed request models, and `SearchItemsResource`/`GetItemsResource`/`GetBrowseNodesResource` enum constants.
- Product model `from_api_response()` updated to accept SDK `Item` objects with typed getters (`getAsin()`, `getItemInfo()`, `getImages()`, `getOffersV2()`, etc.) instead of raw associative arrays.
- Minimum PHP version raised from 7.4 to **8.1** (required by the SDK).

### Removed
- `RequestSigner` class (`class-request-signer.php`) — deleted entirely; SDK handles OAuth 2.0 token management internally via `OAuth2TokenManager`.
- `Marketplace::get_token_endpoint()`, `Marketplace::get_endpoint()`, `Marketplace::version_to_region()` — SDK manages API endpoints and token resolution internally.
- `Marketplace::API_HOST`, `$cognito_endpoints`, `$lwa_endpoints` constants/properties — no longer needed.

## [2.0.0] - 2026-03-11

### Breaking
- **Amazon Creators API Migration** — AzonMate now uses the Amazon Creators API instead of PA-API 5.0. This is a complete rewrite of the API authentication and communication layer. Users must generate new Creators API credentials (Credential ID, Credential Secret, and Version) from their Amazon Associates account.

### Added
- **OAuth 2.0 Token Client** — new `RequestSigner` class that acquires Bearer tokens from Amazon Cognito (v2.x credentials) or Login with Amazon (v3.x credentials), with automatic caching in WordPress transients (3500-second TTL).
- **Single API Endpoint** — all 22 marketplaces now use `creatorsapi.amazon/catalog/v1/` instead of per-region `webservices.amazon.*` hosts.
- **Credential Version Setting** — new dropdown in Settings → API for selecting credential version (2.1 NA, 2.2 EU, 2.3 FE, 3.1–3.3 LwA variants).
- **`x-marketplace` Header** — sent with every API request for proper marketplace routing.
- **Token Endpoint Mapping** — `Marketplace::get_token_endpoint()` and `Marketplace::version_to_region()` methods for resolving OAuth endpoints by credential version.

### Changed
- All API request parameters migrated from PascalCase to lowerCamelCase (`Keywords` → `keywords`, `SearchIndex` → `searchIndex`, etc.).
- All API response keys migrated from PascalCase to lowerCamelCase (`SearchResult` → `searchResult`, `ItemsResult` → `itemsResult`, etc.).
- `Offers.Listings.*` resources replaced with `offersV2.listings.*` — price data now nested inside `price.money.*` with `savingBasis` inside `price`.
- Settings fields: "Access Key" → "Credential ID", "Secret Key" → "Credential Secret", new "Credential Version" dropdown.
- Product model `from_api_response()` updated for all new Creators API field paths.
- Marketplace class: removed per-marketplace `host` entries, replaced AWS regions with API regions (NA/EU/FE).
- Activator, uninstall, manual products, and all documentation updated for new credential names.

### Removed
- `CustomerReviews.StarRating` and `CustomerReviews.Count` resources — not available in the Creators API. Rating fields default to 0.
- `Offers.Listings.DeliveryInfo.IsPrimeEligible` — not available in the Creators API. Prime field defaults to false.
- `PartnerType` parameter — removed from all API request payloads (not used by Creators API).
- AWS Signature v4 signing (HMAC-SHA256) — entire implementation replaced by OAuth 2.0 Bearer token authentication.
- `Marketplace::get_host()` and `Marketplace::get_amz_target()` methods — no longer needed.

## [1.6.1] - 2026-03-11

### Added
- **Fetch from Amazon Button** — new "Fetch from Amazon" button in the product creation/editing form. Enter an ASIN, click Fetch, and all form fields (title, price, image, rating, features, brand, availability, etc.) are auto-populated from the Amazon Creators API.
- **Inline ASIN Row Layout** — ASIN input and Fetch button displayed side-by-side in a flex container with loading spinner feedback.
- **ASIN Format Validation** — soft validation warns if the entered ID doesn't match the standard 10-character ASIN format, with option to proceed anyway.

### Changed
- Manual product entry remains fully supported — fetched data can be reviewed and overridden before saving.
- Readme feature descriptions refreshed for clarity.
- Version bumped to 1.6.1.

## [1.6.0] - 2026-02-24

### Added
- **Dynamic Product Collage** — new `[azonmate collage="ASIN1,ASIN2,..."]` shortcode with auto-adjusting grid layout (hero, duo, trio, quad, penta, auto) based on product count.
- **Collage Hover Behavior** — only the hovered product reveals its Buy button and action elements; non-hover state displays title, price, rating, and discount clearly.
- **Collage Gutenberg Block** — 8th block: search/add products, live server-side preview with InspectorControls for max products, gap, badges, price, rating, and button text.
- **Per-product Fetch Button** — individually refresh any product from Amazon API with one click from the Products admin page.
- **Master Fetch Button** — bulk refresh ALL products from Amazon API in Settings → Cache tab; batch fetches with rate limiting, updates pricing, discounts, ratings, availability, and images globally.
- **`CacheManager::get_all_product_asins()`** — new method returning all stored ASINs grouped by marketplace for batch operations.

### Fixed
- **Bullet Feature Alignment** — product box feature checkmark icons and text now properly aligned with consistent spacing using CSS `::before` pseudo-elements instead of `list-style: disc`.

### Changed
- Webpack config now builds 8 per-block JS bundles (added collage).
- Version bumped to 1.6.0.

## [1.5.0] - 2026-02-23

### Added
- **Showcase Gutenberg Block** — new block with a 3-step editor: select from 8 pre-built layouts (Grid, List, Masonry, Table, Hero, Compact, Split, Deal), search & pick products, then see a live server-side preview with full InspectorControls for layout, columns, max items, and display toggles.
- **Orange Brand Icons** — all 7 block icons in the inserter and placeholders now use the AzonMate brand color (#ff9900) with custom SVG designs.

### Changed
- **Author Name** — updated author credit from "Numan" to "Numan Rashed" across the plugin header, admin bar, footer, LICENSE, README, and changelog.

## [1.4.0] - 2026-02-23

### Added
- **Gutenberg Blocks** — 6 fully functional blocks: Product Box, Product List, Comparison Table, Bestsellers, Text Link, and Product Search.
- **Block Inserter Discovery** — all blocks searchable by "azonmate", "amazon", "product", "affiliate" in the block inserter.
- **In-Editor Product Search** — search Amazon products, browse saved/manual products, or paste ASINs directly inside each block.
- **Server-Side Rendering** — all blocks render via the shortcode engine for consistent front-end output.
- **Shared Editor CSS** — search panel, result cards, selected product tags, and category picker styles.
- **AzonMate Product Search Block** — universal block with display type chooser (box / link / image).

### Changed
- Webpack config now builds 6 separate per-block JS bundles plus copies block.json, render.php, and editor.css into the `build/` directory.
- BlockRegistrar refactored to singleton pattern with per-block JS enqueuing and `index.asset.php` dependency loading.
- Version bumped to 1.4.0.

## [1.3.5] - 2026-02-23

### Changed
- **Branded Admin Bar Logo** — replaced the generic smiley-face SVG in the admin bar with the official AzonMate icon (orange rounded square, white text, Amazon smile arrow with arrowhead).
- Updated `icon.svg` — white text/stroke, proper arrowhead polygon, increased corner radius (`rx=14`).
- Version bumped to 1.3.5.

## [1.3.4] - 2026-02-23

### Added
- **Unified Disclosure System** — merged the old "Disclosure" (per-showcase) and "Disclaimer" (footer) into one clean system under Display settings.
- **Customizable Disclosure** — custom text, font size (10–14px), text color (hex), and alignment (left/center/right) all configurable from the Display tab.
- **Ad-Type Box Sizes** — new `size` shortcode attribute for standard ad unit dimensions: `300x250`, `336x280`, `728x90`, `160x600`, `970x250`. Usage: `[azonmate showcase="..." size="300x250"]`.
- Ad-type size dropdown added to the Showcase Builder "Optional Extras" step.
- Showcase Builder WYSIWYG preview now reflects the selected ad-type box size.

### Changed
- Disclosure only renders when the checkbox is enabled — no more duplicate text in footer + cards.
- Removed old footer disclaimer hook from `class-plugin.php` (`render_disclaimer()` method and `wp_footer` action removed entirely).
- Removed 3 dead Advanced-tab settings: `azon_mate_show_disclaimer`, `azon_mate_disclaimer_text`, `azon_mate_disclaimer_position`.
- Showcase CSS v3: all critical layout properties now use `!important` for rock-solid theme isolation (container, card, button, image, link resets).
- Added `p`, `span` element resets inside `.azonmate-showcase` to prevent theme typography overrides.
- Version bumped to 1.3.4.

### Fixed
- Disclosure text was hardcoded in `azon_mate_render_disclosure()` — now reads the customizable `azon_mate_disclosure_text` option.
- Disclaimer "Position" dropdown was a dead option (only `wp_footer` was hooked, `before_content`/`after_content` never implemented) — removed.
- Disclosure appearing both after cards AND in footer when both old systems were enabled.

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
- **Admin brand bar** on every plugin page — dark header with AzonMate logo, version badge, "by Numan Rashed" author credit, GitHub repo link, and "Star on GitHub" CTA button.
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
- **Amazon Creators API** integration with OAuth 2.0 authentication.
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
