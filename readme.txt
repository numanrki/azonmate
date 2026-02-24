=== AzonMate – Amazon Affiliate Product Engine ===
Contributors: azonmate
Tags: amazon, affiliate, product, comparison table, bestseller
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.6.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Search, display, and monetize Amazon products directly from your WordPress posts with auto-updating prices, comparison tables, bestseller lists, and more.

== Description ==

**AzonMate** is a full-featured WordPress plugin that connects to the **Amazon Product Advertising API (PA-API 5.0)** to let you search, embed, and beautifully display Amazon affiliate products inside posts, pages, and widgets.

= Key Features =

* **Live Product Search** – Search Amazon directly from the WordPress editor (Classic & Gutenberg).
* **Auto-Updating Prices** – Product data is cached and automatically refreshed via WP-Cron.
* **Multiple Display Types** – Product boxes, text links, image links, product lists, comparison tables, and bestseller lists.
* **Gutenberg Blocks** – Native blocks for all display types with live preview.
* **Shortcodes** – Powerful shortcode system with numerous attributes for customization.
* **Comparison Tables** – Side-by-side product comparisons with customizable columns.
* **Bestseller Lists** – Auto-generated lists from Amazon categories.
* **Geo-Targeting** – Automatically redirect visitors to their local Amazon store.
* **Click Tracking** – Built-in analytics for affiliate link clicks.
* **Template System** – Fully customizable templates with theme override support.
* **Responsive Design** – Mobile-first, dark mode support, BEM CSS architecture.
* **Privacy Focused** – GDPR-friendly IP hashing, encrypted API key storage.

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
* PHP 7.4 or higher
* Amazon Product Advertising API credentials
* Amazon Associates account

== Installation ==

1. Upload the `azonmate` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **AzonMate → Settings** and enter your Amazon PA-API credentials.
4. Click **Test Connection** to verify your API keys.
5. Start using shortcodes or Gutenberg blocks to display products!

== Frequently Asked Questions ==

= Do I need an Amazon Associates account? =

Yes, you need an approved Amazon Associates account and PA-API 5.0 credentials.

= How often is product data updated? =

By default, cached data is refreshed every 24 hours. You can configure this in Settings → Cache.

= Does this work with the Gutenberg editor? =

Yes! AzonMate includes native Gutenberg blocks with live previews and search integration.

= Can I override the templates? =

Yes, copy any template from `plugins/azonmate/templates/` to `your-theme/azonmate/` and customize it.

= Does it support multiple Amazon marketplaces? =

Yes, AzonMate supports 10 marketplaces: US, UK, DE, FR, IN, CA, JP, IT, ES, and AU. With geo-targeting enabled, visitors are automatically directed to their local store.

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
* PA-API 5.0 integration with ten marketplaces
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

= 1.0.0 =
Initial release of AzonMate.
