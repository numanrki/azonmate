<p align="center">
  <img src="assets/images/icon.svg" width="80" alt="AzonMate Logo" />
</p>

<h1 align="center">AzonMate</h1>

<p align="center">
  <strong>Amazon Affiliate Product Engine for WordPress</strong><br />
  Search, display & monetize Amazon products directly from your WordPress posts.
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#showcase-layouts">Showcase Layouts</a> •
  <a href="#shortcodes">Shortcodes</a> •
  <a href="#installation">Installation</a> •
  <a href="#configuration">Configuration</a> •
  <a href="#changelog">Changelog</a> •
  <a href="#license">License</a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/version-1.3.0-ff9900?style=flat-square" alt="Version 1.3.0" />
  <img src="https://img.shields.io/badge/WordPress-6.0%2B-21759b?style=flat-square&logo=wordpress" alt="WordPress 6.0+" />
  <img src="https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=flat-square&logo=php" alt="PHP 7.4+" />
  <img src="https://img.shields.io/badge/license-Personal%20Use-blue?style=flat-square" alt="License: Free for Personal Use" />
</p>

---

## What is AzonMate?

AzonMate is a powerful WordPress plugin that connects to the **Amazon Product Advertising API 5.0** to let you search, cache, and beautifully display Amazon products inside any post, page, or widget. It handles affiliate link management, geo-targeting, click tracking, and analytics — all from one plugin.

Whether you run a product review blog, a niche affiliate site, or an online magazine with deal roundups, AzonMate gives you **8 showcase layouts**, **7 shortcodes**, **Gutenberg blocks**, and a visual **Showcase Builder** to create stunning product displays without writing a single line of code.

---

## Features

### 🔗 Amazon PA-API 5.0 Integration
- Full AWS Signature v4 authentication
- Support for **10 Amazon marketplaces**: US, UK, DE, FR, IN, CA, JP, IT, ES, AU
- Real-time product search from the WordPress admin
- Automatic price, rating, and availability syncing

### 🎨 8 Showcase Layouts
Beautiful, theme-proof product displays that look identical in the admin preview and on your live site:

| Multi-Product | Single-Product |
|---|---|
| **Grid Cards** — Responsive card grid with badges & CTA | **Hero Card** — Large featured product spotlight |
| **Row List** — Horizontal rows with image, details & price | **Compact** — Slim inline card for mid-article placement |
| **Masonry** — Pinterest-style staggered collage | **Split** — 50/50 image + details panels |
| **Comparison Table** — Side-by-side columns | **Deal Card** — Price-drop focused with savings emphasis |

### 📦 7 Shortcodes
| Shortcode | Purpose |
|---|---|
| `[azonmate box="ASIN"]` | Product box (default, horizontal, compact) |
| `[azonmate link="ASIN"]text[/azonmate]` | Inline text link |
| `[azonmate image="ASIN"]` | Linked product image |
| `[azonmate field="price" asin="ASIN"]` | Individual data field |
| `[azonmate list="ASIN1,ASIN2"]` | Product list (vertical/grid) |
| `[azonmate table="ASIN1,ASIN2"]` | Comparison table |
| `[azonmate showcase="ASIN" layout="hero"]` | Showcase (8 layouts) |

### 🧱 Gutenberg Blocks
Native block editor support for:
- Product Box
- Product List
- Comparison Table
- Bestseller / New Releases
- Text Link

### 🛠️ Visual Showcase Builder
A point-and-click admin page to build showcase shortcodes:
1. **Pick a Design** — Choose from 8 layouts across two categories
2. **Select Products** — Click to pick from your product library
3. **Optional Extras** — Set heading, button text, columns
4. **Copy Shortcode** — One-click copy with live WYSIWYG preview

The builder preview uses the **exact same CSS** as the frontend, so what you see is what your visitors get.

### ⚡ Smart Caching
- Database-backed product cache with configurable TTL
- WP-Cron scheduled refresh to keep prices & availability current
- Automatic stale data cleanup

### 🌍 Geo-Targeting
- Detects visitor country via CloudFlare headers or ip-api.com fallback
- Automatically swaps affiliate tags per marketplace
- Redirects to the correct Amazon store for each visitor

### 📊 Click Tracking & Analytics
- Privacy-safe IP hashing (SHA-256 with daily rotating salt)
- Per-product and per-post click statistics
- Admin dashboard widget for quick stats
- CSV export for reporting

### 🔒 Security
- AES-256-CBC encryption for API credentials
- AJAX nonce verification on all endpoints
- Capability checks (`manage_options`) for admin actions
- Rate limiting on search requests (5 per 10 seconds)

### 🎨 Theming & Customization
- CSS Custom Properties for easy color/spacing overrides
- BEM naming convention throughout all CSS
- Dark mode support via `prefers-color-scheme`
- Template override system: copy any template to your theme to customize
- Fully scoped CSS — no conflicts with your theme or other plugins

### ✋ Manual Products
- Add products manually without an API connection
- Full CRUD interface in the admin panel
- Works with all shortcodes and showcase layouts

---

## Showcase Layouts

### Multi-Product Layouts

**Grid Cards** — Responsive cards that adapt to 1–4 columns. Includes product badge, savings percentage, brand, title, rating, price, description, and a prominent buy button.

**Row List** — Full-width horizontal rows with a thumbnail on the left, product details in the center, and price + CTA on the right. Highlighted rows for badged products.

**Masonry** — Pinterest-style staggered layout using CSS columns. Automatically flows products into the available space for a dynamic, magazine-like feel.

**Comparison Table** — Data-rich table with columns per product. Rows for image, title, rating, price, and buy button. Highlighted columns for recommended products.

### Single-Product Layouts

**Hero Card** — A large featured card with an image panel (42% width) and a body section. Ideal for top-of-page featured product spotlights.

**Compact** — A slim inline card that sits naturally between paragraphs. Thumbnail, title, rating & price in one row with a CTA button.

**Split** — A clean 50/50 grid with the product image on one side and full details (features, description, price, CTA) on the other.

**Deal Card** — Designed for price-drop promotions. Features an orange accent strip, savings percentage badge, and prominent "Save X%" callout.

---

## Installation

### From GitHub (Manual)

1. Download or clone this repository:
   ```bash
   git clone https://github.com/flavor-developer/azonmate.git
   ```
2. Copy (or symlink) the `azonmate` folder into `wp-content/plugins/`.
3. Activate the plugin from **Plugins → Installed Plugins** in your WordPress admin.

### From ZIP

1. Go to **Plugins → Add New → Upload Plugin** in WordPress.
2. Upload the ZIP archive.
3. Click **Activate**.

---

## Configuration

1. Navigate to **AzonMate → Settings** in the WordPress admin.
2. Enter your **Amazon PA-API 5.0** credentials:
   - Access Key
   - Secret Key
   - Partner Tag
   - Marketplace
3. Click **Test Connection** to verify.
4. Configure caching, geo-targeting, and tracking preferences in the remaining tabs.

---

## Usage

### Shortcode Examples

```
[azonmate box="B0DCCNPFVC"]
[azonmate showcase="B0DCCNPFVC" layout="hero"]
[azonmate showcase="B0DCCNPFVC,B09V3KXJPB,B0BSHF7WHW" layout="grid" columns="3" heading="Our Top Picks"]
[azonmate table="B0DCCNPFVC,B09V3KXJPB" highlight="1"]
[azonmate link="B0DCCNPFVC"]Check this out[/azonmate]
```

### Showcase Builder

Go to **AzonMate → Showcase** in the admin to use the visual builder — no shortcode syntax to memorize.

---

## Requirements

| Requirement | Version |
|---|---|
| WordPress | 6.0 or higher |
| PHP | 7.4 or higher |
| Amazon PA-API | 5.0 credentials |

---

## File Structure

```
azonmate/
├── azonmate.php              # Main plugin entry point
├── uninstall.php             # Clean uninstall handler
├── assets/
│   ├── css/                  # Admin, public, editor, showcase styles
│   ├── js/                   # Admin, public, click-tracker, search modal
│   └── images/               # Icons & SVGs
├── includes/
│   ├── admin/                # Settings, analytics, showcase builder, manual products
│   ├── api/                  # Amazon API client & request signer
│   ├── blocks/               # Gutenberg block registrar & source
│   ├── cache/                # Cache manager & cron refresh
│   ├── geo/                  # Geo-targeting & link rewriter
│   ├── models/               # Product data model
│   ├── shortcodes/           # All 7 shortcode handlers
│   ├── templates/            # Template renderer & helper functions
│   └── tracking/             # Click tracker
├── templates/                # Frontend display templates (overridable)
│   ├── bestseller/
│   ├── comparison-table/
│   ├── image-link/
│   ├── product-box/
│   ├── product-list/
│   ├── showcase/             # 8 showcase layout templates
│   └── text-link/
└── languages/                # Translation .pot file
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for the full version history.

### Latest — v1.3.0
- **New:** 4 single-product showcase layouts — Hero, Compact, Split, Deal
- **New:** Visual Showcase Builder with WYSIWYG preview (admin preview = live post)
- **Fixed:** Critical buy-button class mismatch causing unstyled CTA buttons
- **Improved:** All showcase CSS fully scoped to prevent theme/plugin conflicts
- **Improved:** Consistent responsive breakpoints (960px / 600px) across all layouts

---

## Download & Install

1. **Download** the latest release ZIP from the [Releases page](https://github.com/numanrki/azonmate/releases) or click **Code → Download ZIP** above.
2. In your WordPress admin go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP file and click **Install Now**.
4. Click **Activate** — done!

---

## Author

Created & maintained by **Numan** ([@numanrki](https://github.com/numanrki)).

---

## License

```
Copyright (C) 2026 Numan / AzonMate. All rights reserved.

This software is provided FREE for PERSONAL, non-commercial use only.

You MAY:
  - Download and install AzonMate on your own WordPress site(s)
  - Modify the code for your own personal use

You MAY NOT:
  - Redistribute, resell, sublicense, or share this software
  - Include it in any product, theme, plugin bundle, or SaaS offering
  - Remove or alter this copyright notice

THIS SOFTWARE IS PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND.
USE AT YOUR OWN RISK.
```

---

<p align="center">
  Built with ☕ by <strong><a href="https://github.com/numanrki">Numan</a></strong> for the Amazon affiliate community.<br />
  <strong><a href="https://azonmate.com">azonmate.com</a></strong>
</p>
