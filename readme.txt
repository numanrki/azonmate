=== AzonMate – Amazon Affiliate Product Engine ===
Contributors: azonmate
Tags: amazon, affiliate, product, comparison table, bestseller
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 8.1
Stable tag: 2.3.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Search, display, and monetize Amazon products directly from your WordPress posts with auto-updating prices, comparison tables, bestseller lists, and more.

== Description ==

**AzonMate** is a full-featured WordPress plugin that connects to the **Amazon Creators API** to let you search, embed, and beautifully display Amazon affiliate products inside posts, pages, and widgets.

= Key Features =

* **Instant Amazon Search** – Find and insert Amazon products from within the WordPress editor (Classic & Gutenberg).
* **Automatic Price Updates** – Product details are cached and refreshed automatically using WP-Cron.
* **Flexible Display Options** – Choose from product boxes, text links, image links, product lists, comparison tables, bestseller lists, and collages.
* **Gutenberg Block Support** – Includes native blocks for every display type with real-time previews.
* **Advanced Shortcodes** – Versatile shortcode system with extensive customization options.
* **Product Comparison Tables** – Create side-by-side comparisons with configurable columns.
* **Bestseller & Collage Lists** – Generate bestseller and dynamic collage layouts from Amazon categories.
* **Marketplace Geo-Targeting** – Direct visitors to their local Amazon store automatically.
* **Affiliate Click Analytics** – Track and analyze affiliate link clicks with built-in reporting.
* **Customizable Templates** – Override and personalize templates directly from your theme.
* **Mobile-Ready & Dark Mode** – Responsive layouts, BEM CSS, and full dark mode compatibility.
* **Privacy-First Approach** – GDPR-compliant IP hashing and encrypted API credentials.
* **One-Click Updates** – Check for new releases and install updates directly from the plugin Settings page.
* **Dashboard Update Notifications** – Get notified on your WordPress dashboard when a new version is available.

= Shortcodes =

* `[azonmate box="ASIN"]` – Full product card
* `[azonmate link="ASIN"]anchor text[/azonmate]` – Text link
* `[azonmate image="ASIN"]` – Image link
* `[azonmate field="price" asin="ASIN"]` – Single data point
* `[azonmate list="ASIN1,ASIN2,ASIN3"]` – Product list
* `[azonmate bestseller="Category"]` – Bestseller list
* `[azonmate table="ASIN1,ASIN2"]` – Comparison table

= Requirements =

* WordPress 6.0 or higher
* PHP 8.1 or higher
* Amazon Creators API credentials
* Amazon Associates account

== Installation ==

1. Upload the `azonmate` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **AzonMate → Settings** and enter your Amazon Creators API credentials.
4. Click **Test Connection** to verify your API credentials.
5. Start using shortcodes or Gutenberg blocks to display products!

== Frequently Asked Questions ==

= Do I need an Amazon Associates account? =

Yes, you need an approved Amazon Associates account and Creators API credentials.

= How often is product data updated? =

By default, cached data is refreshed every 24 hours. You can configure this in Settings → Cache.

= Does this work with the Gutenberg editor? =

Yes! AzonMate includes native Gutenberg blocks with live previews and search integration.

= Can I override the templates? =

Yes, copy any template from `plugins/azonmate/templates/` to `your-theme/azonmate/` and customize it.

= Does it support multiple Amazon marketplaces? =

Yes, AzonMate supports all 22 Amazon Creators API marketplaces across three regions:

* **NA** — US, CA, MX, BR
* **EU** — UK, DE, FR, IT, ES, NL, BE, PL, SE, TR, SA, AE, EG, IE, IN
* **FE** — JP, SG, AU

With geo-targeting enabled, visitors are automatically directed to their local store.

= Is it GDPR compliant? =

AzonMate hashes IP addresses before storage and includes options for anonymization. No personal data is sent to third-party services beyond the Amazon API.

== Screenshots ==

1. Product Box - Default template
2. Product Box - Horizontal layout
3. Comparison Table
4. Settings page
5. Product search modal
6. Analytics dashboard
7. Gutenberg block editor

== Changelog ==

= 2.3.4 =
* Fixed: Critical error after one-click update — old plugin folder was not removed before move, causing rename failure
* Fixed: Removed activate_plugin() call from upgrader_post_install hook — WordPress handles reactivation automatically

= 2.3.3 =
* Maintenance: Test release to verify one-click update flow end-to-end

= 2.3.2 =
* Fixed: "Install Update" failed because WordPress's update transient did not contain the download URL — now injected before upgrade

= 2.3.1 =
* Improved: Update notification banner now displays on all WordPress admin pages, not just AzonMate screens
* Improved: readme.txt and README.md updated to document one-click updates and dashboard notifications

= 2.3.0 =
* Added: Updates tab in Settings — check for new releases and install updates with one click directly from the plugin
* Added: Admin notification banner on all AzonMate pages when a new version is available
* Added: Live install — click "Install Update" to download and apply the latest release without leaving the page

= 2.2.2 =
* Added: GitHub-based auto-updater — plugin checks for new releases and updates via WordPress's native update system
* Improved: Version constant now derived from plugin header — single edit point for PHP versioning

= 2.2.1 =
* Added: Buy Me a Coffee donation button in admin header bar alongside Star on GitHub

= 2.2.0 =
* Added: 12 new Amazon marketplaces — MX, BR, NL, BE, PL, SE, TR, SA, AE, EG, IE, SG (total: 22 across NA, EU, FE)
* Fixed: Shortcode fallback URL used marketplace code instead of domain — now resolves via Marketplace class
* Refactored: LinkRewriter reads domains from central Marketplace class instead of hardcoded map

= 2.1.3 =
* Fixed: Product card button icons (Edit, Fetch, Delete, Copy Shortcode) misaligned with text — now uses flexbox centering

= 2.1.2 =
* Reduced plugin distribution size from ~4 MB to ~2.5 MB (35% smaller)
* Removed duplicate SDK source in lib/ — vendor/ already contains the copied SDK
* Removed vendor doc cruft (CHANGELOG, README, UPGRADING files from dependencies)
* Removed dev-only root files (package.json, webpack.config.js, composer.lock) from distribution
* Cleaned composer.json — removed path repository reference

= 2.1.1 =
* Fixed: Critical error on deployment — vendor/ directory (SDK + Guzzle dependencies) was excluded from distribution by .gitignore
* Fixed: Composer path repository used symlink instead of file copy, breaking SDK autoloading on production servers

= 2.1.0 =
* Integrated official Amazon Creators API PHP SDK (v1.2.0) — replaces custom HTTP/OAuth with typed SDK classes
* Composer-managed dependencies — Guzzle HTTP 7.x, PSR-7, PSR-HTTP-Client via autoloader
* Amazon_API class rewritten to use SDK DefaultApi, typed request models, and resource enum constants
* Product model from_api_response() updated to accept SDK Item objects with typed getters
* Minimum PHP version raised from 7.4 to 8.1 (SDK requirement)
* Removed: RequestSigner class — SDK handles OAuth 2.0 token management internally
* Removed: Marketplace token/endpoint methods — SDK manages endpoints internally

= 2.0.0 =
* **Breaking:** Migrated from Amazon PA-API 5.0 to the new Amazon Creators API with OAuth 2.0 authentication
* OAuth 2.0 token client — automatic Bearer token acquisition and caching (1-hour TTL) via Amazon Cognito or Login with Amazon
* Single API host `creatorsapi.amazon` for all 22 marketplaces
* New Credential Version selector in Settings (2.1 NA, 2.2 EU, 2.3 FE, 3.1–3.3 LwA)
* All API parameters and response keys migrated from PascalCase to lowerCamelCase
* Offers resources replaced with offersV2 — price now nested in `price.money.*`
* Settings fields renamed: Access Key → Credential ID, Secret Key → Credential Secret, plus Version dropdown
* Removed: CustomerReviews (star rating, review count) — no longer available in Creators API
* Removed: DeliveryInfo.IsPrimeEligible (Prime badge) — no longer available in Creators API
* Removed: AWS Signature v4 request signing — replaced by OAuth 2.0 client

= 1.6.1 =
* Fetch from Amazon button — enter an ASIN in the product form and click "Fetch from Amazon" to auto-populate all fields (title, price, image, features, etc.) directly from Amazon Creators API
* Manual entry still fully supported — fetched data can be reviewed and overridden before saving
* Improved readme feature descriptions

= 1.6.0 =
* Dynamic Product Collage — new `[azonmate collage="ASIN1,ASIN2,..."]` shortcode with auto-adjusting grid layout (hero, duo, trio, quad, penta, auto) based on product count
* Collage hover behavior — only the hovered product reveals its Buy button; non-hover state displays title, price, rating, and discount
* Collage Gutenberg Block — search/add products, live server-side preview with InspectorControls for max products, gap, badges, price, rating, button text
* Per-product Fetch button — individually refresh any product from Amazon API with one click from the Products page
* Master Fetch button — bulk refresh ALL products from Amazon API in Settings → Cache tab; updates pricing, discounts, ratings, availability, and images globally
* Bullet feature alignment fix — product box checkmark icons and text now properly aligned with consistent spacing
* New collage CSS with responsive breakpoints and smooth hover transitions

= 1.5.0 =
* Showcase Gutenberg Block — 3-step editor: pick from 8 pre-built layouts, search & select products, live server-side preview
* Orange brand icons (#ff9900) for all 7 blocks in the inserter and placeholders
* Author name updated to "Numan Rashed" throughout the plugin

= 1.4.0 =
* Gutenberg blocks — 6 fully functional blocks: Product Box, Product List, Comparison Table, Bestsellers, Text Link, and Product Search
* Block Inserter discovery — all blocks searchable by "azonmate", "amazon", "product", "affiliate"
* In-editor product search — search Amazon products, browse saved/manual products, or paste ASINs directly inside each block
* Server-side rendering — all blocks render via shortcode engine for consistent front-end output
* Shared editor CSS with search panel, result cards, selected product tags, and category picker
* New "AzonMate Product Search" universal block with display type chooser (box/link/image)

= 1.3.5 =
* Branded admin bar logo — official AzonMate icon replaces generic smiley SVG
* Updated icon.svg with arrowhead and improved styling

= 1.3.4 =
* Unified disclosure system — customizable text, font size, color, alignment
* Ad-type box sizes: 300x250, 336x280, 728x90, 160x600, 970x250
* Size dropdown in Showcase Builder with live preview
* Fixed disclosure duplication (no more footer + cards overlap)
* Hardened showcase CSS with !important for theme-proof isolation
* Removed dead footer disclaimer system

= 1.3.3 =
* AJAX tab switching on Settings page — no page reload
* Page Hero headers on all admin pages
* Modern pill-style tab navigation with dashicons
* Card-wrapped settings forms with branded submit buttons
* Tab state preserved after form save

= 1.0.0 =
* Initial release
* Creators API integration with ten marketplaces
* Product Box, Text Link, Image Link shortcodes
* Product List and Comparison Table shortcodes
* Bestseller and New Releases shortcodes
* Gutenberg blocks with live preview
* Smart caching with WP-Cron refresh
* Geo-targeting with automatic tag swapping
* Click tracking and analytics dashboard
* Template override system
* Responsive CSS with dark mode support

== Upgrade Notice ==

= 2.0.0 =
**Breaking change:** AzonMate now uses the Amazon Creators API instead of PA-API 5.0. You must generate new Creators API credentials (Credential ID, Credential Secret, and Version) from your Amazon Associates account. PA-API 5.0 is being deprecated on April 30, 2026.

= 1.0.0 =
Initial release of AzonMate.
