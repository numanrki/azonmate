<p align="center">
  <img src="assets/images/icon.svg" width="90" alt="AzonMate" />
</p>

<h1 align="center">AzonMate</h1>

<p align="center">
  <strong>The Amazon Affiliate Product Engine for WordPress</strong>
</p>

<p align="center">
  <a href="https://github.com/numanrki/azonmate/releases/latest"><img src="https://img.shields.io/badge/version-1.4.0-ff9900?style=for-the-badge" alt="v1.4.0" /></a>&nbsp;
  <img src="https://img.shields.io/badge/WordPress-6.0%2B-21759b?style=for-the-badge&logo=wordpress&logoColor=white" alt="WordPress 6.0+" />&nbsp;
  <img src="https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 7.4+" />&nbsp;
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-Free_for_Personal_Use-22c55e?style=for-the-badge" alt="Free for Personal Use" /></a>
</p>

<br />

<!-- ╔══════════════════════════════════════════════════════════════╗ -->
<!-- ║  NOTICE                                                      ║ -->
<!-- ╚══════════════════════════════════════════════════════════════╝ -->

> [!NOTE]
> **AzonMate is free, open-source, and community-driven.**
>
> Most Amazon affiliate plugins charge **$49 – $199/year**. AzonMate gives you the same power — 8 stunning layouts, Gutenberg blocks, geo-targeting, analytics — for **$0**. No premium tier, no upsells, no nag banners.
>
> Our goal is simple: **build the best free Amazon affiliate toolkit on the planet**, together. Star the repo, share it with fellow affiliate marketers, submit ideas or pull requests — every contribution makes AzonMate better for everyone.
>
> If you find AzonMate useful, the best way to support it is to **give it a** ⭐ **on GitHub** and tell your friends.

<br />

<p align="center">
  <a href="#-quick-start">Quick Start</a>&nbsp;&nbsp;·&nbsp;&nbsp;
  <a href="#-features">Features</a>&nbsp;&nbsp;·&nbsp;&nbsp;
  <a href="#-showcase-layouts">Layouts</a>&nbsp;&nbsp;·&nbsp;&nbsp;
  <a href="#-shortcodes">Shortcodes</a>&nbsp;&nbsp;·&nbsp;&nbsp;
  <a href="#-configuration">Configuration</a>&nbsp;&nbsp;·&nbsp;&nbsp;
  <a href="#-changelog">Changelog</a>&nbsp;&nbsp;·&nbsp;&nbsp;
  <a href="#-license">License</a>
</p>

---

## 🚀 Quick Start

1. **Download** the latest `.zip` from the [Releases page](https://github.com/numanrki/azonmate/releases/latest) — or click the green **Code** button above → **Download ZIP**.
2. In WordPress go to **Plugins → Add New → Upload Plugin** and upload the zip.
3. Click **Install Now**, then **Activate**.
4. Head to **AzonMate → Settings**, enter your Amazon PA-API 5.0 credentials, hit **Test Connection** — done.

That's it. Start dropping shortcodes or using the visual Showcase Builder.

---

## ✨ Features

<table>
<tr>
<td width="50%" valign="top">

### Amazon PA-API 5.0
- AWS Signature v4 authentication
- **10 marketplaces** — US, UK, DE, FR, IN, CA, JP, IT, ES, AU
- Real-time product search inside the editor
- Live price, rating & availability sync

### 8 Showcase Layouts
Theme-proof product displays — admin preview = live post:

| Multi-Product | Single-Product |
|:---|:---|
| Grid Cards | Hero Card |
| Row List | Compact |
| Masonry | Split |
| Comparison Table | Deal Card |

### 7 Shortcodes
`box` · `link` · `image` · `field` · `list` · `table` · `showcase`

### Gutenberg Blocks
Product Box · Product List · Comparison Table · Bestseller · Text Link

</td>
<td width="50%" valign="top">

### Visual Showcase Builder
Point-and-click shortcode generator with live WYSIWYG preview — no code required.

### Smart Caching
Database-backed cache with configurable TTL + WP-Cron auto-refresh.

### Geo-Targeting
Auto-detect visitor country → swap affiliate tag → redirect to correct Amazon store.

### Click Tracking & Analytics
Privacy-safe tracking with SHA-256 hashed IPs, per-product stats, dashboard widget, CSV export.

### Security First
AES-256-CBC key encryption · nonce-verified AJAX · capability checks · rate limiting.

### Theme-Proof CSS
Fully scoped BEM classes · CSS Custom Properties · dark mode · template overrides.

### Manual Products
Add products manually without an API key — full CRUD admin interface.

</td>
</tr>
</table>

---

## 🎨 Showcase Layouts

### Multi-Product

| Layout | Description |
|:---|:---|
| **Grid Cards** | Responsive 1–4 column card grid with badges, savings %, ratings, and a prominent CTA button. |
| **Row List** | Full-width horizontal rows — thumbnail left, details center, price + button right. Highlighted rows for badged products. |
| **Masonry** | Pinterest-style CSS-column layout. Products flow dynamically for a magazine feel. |
| **Comparison Table** | Data-rich column-per-product table — image, title, rating, price, buy button. Highlight the recommended pick. |

### Single-Product

| Layout | Description |
|:---|:---|
| **Hero Card** | Large spotlight card with 42% image panel + full body. Perfect for "top pick" callouts. |
| **Compact** | Slim inline card that sits between paragraphs — thumbnail, title, price, CTA in one row. |
| **Split** | Clean 50/50 grid — image on one side, details + features + CTA on the other. |
| **Deal Card** | Price-drop focused design with orange accent, savings % badge, and "Save X%" callout. |

---

## 📦 Shortcodes

```text
[azonmate box="ASIN"]                                          → Product box
[azonmate link="ASIN"]anchor text[/azonmate]                   → Inline text link
[azonmate image="ASIN"]                                        → Product image link
[azonmate field="price" asin="ASIN"]                           → Single data field
[azonmate list="ASIN1,ASIN2" layout="grid"]                    → Product list
[azonmate table="ASIN1,ASIN2" highlight="1"]                   → Comparison table
[azonmate showcase="ASIN1,ASIN2" layout="grid" columns="3"]    → Showcase (8 layouts)
```

> **Tip:** Use the **Showcase Builder** (`AzonMate → Showcase` in the admin) to generate shortcodes visually — no syntax to memorize.

---

## ⚙️ Configuration

| Setting | Where |
|:---|:---|
| API credentials (Access Key, Secret Key, Partner Tag, Marketplace) | **AzonMate → Settings → API** |
| Cache TTL & auto-refresh schedule | **AzonMate → Settings → Cache** |
| Geo-targeting regions & fallback tags | **AzonMate → Settings → Geo** |
| Click tracking & analytics options | **AzonMate → Settings → Tracking** |
| Manual products CRUD | **AzonMate → Products** |
| Visual showcase builder | **AzonMate → Showcase** |

---

## 📋 Requirements

| | Minimum |
|:---|:---|
| WordPress | 6.0 |
| PHP | 7.4 |
| Amazon PA-API | 5.0 credentials |

---

## 📂 Project Structure

```
azonmate/
├── azonmate.php                    → Main plugin bootstrap
├── uninstall.php                   → Clean uninstall handler
├── assets/
│   ├── css/                        → Admin, public, editor, showcase styles
│   ├── js/                         → Admin, public, click tracker, search modal
│   └── images/                     → Icons & SVG assets
├── includes/
│   ├── admin/                      → Settings, analytics, showcase builder, manual products
│   ├── api/                        → Amazon API client & AWS v4 request signer
│   ├── blocks/src/                 → Gutenberg block source (JSX)
│   ├── cache/                      → Cache manager & cron refresh
│   ├── geo/                        → Geo-targeting & link rewriter
│   ├── models/                     → Product data model
│   ├── shortcodes/                 → 7 shortcode handlers + abstract base
│   ├── templates/                  → Template renderer & helper functions
│   └── tracking/                   → Click tracker
├── templates/                      → Frontend templates (theme-overridable)
│   ├── showcase/                   → 8 showcase layout templates
│   ├── product-box/                → Box templates (default, horizontal, compact)
│   ├── comparison-table/           → Table template
│   ├── product-list/               → List templates (default, grid)
│   ├── bestseller/                 → Bestseller template
│   ├── image-link/                 → Image link template
│   └── text-link/                  → Text link template
└── languages/                      → Translation .pot file
```

---

## 📝 Changelog

> Full history in [CHANGELOG.md](CHANGELOG.md).

### v1.4.0 — 2026-02-23
- **New:** 6 fully functional Gutenberg blocks — Product Box, Product List, Comparison Table, Bestsellers, Text Link, and Product Search
- **New:** All blocks searchable in the Block Inserter by "azonmate", "amazon", "product", "affiliate"
- **New:** In-editor product search — search Amazon products, browse saved/manual products, or paste ASINs directly inside each block
- **New:** Server-side rendering for all blocks via the shortcode engine
- **New:** Shared editor CSS with search panel, result cards, selected product tags, and category picker
- **New:** "AzonMate Product Search" universal block with display type chooser (box / link / image)

### v1.3.5 — 2026-02-23
- **Changed:** Admin bar logo now uses the official branded AzonMate icon (orange rounded square, white text, Amazon smile arrow with arrowhead) instead of the generic smiley-face SVG
- **Changed:** `icon.svg` updated — white text/stroke, arrowhead polygon, increased corner radius

### v1.3.4 — 2026-02-23
- **New:** Unified disclosure system — custom text, font size, color, alignment (Display settings)
- **New:** Ad-type box sizes: `300x250`, `336x280`, `728x90`, `160x600`, `970x250` via `size` shortcode attribute
- **New:** Size dropdown in Showcase Builder with live preview
- **Fixed:** Disclosure no longer duplicated in footer + cards — single system, only shows after showcase when enabled
- **Fixed:** Hardened showcase CSS with `!important` on all critical properties — themes can no longer break the design
- **Removed:** Dead footer disclaimer system (3 unused Advanced settings)

### v1.3.3 — 2026-02-23
- **New:** AJAX tab switching on Settings page — instant tab transition, no page reload, hash-based URL state
- **New:** Page Hero headers on all 4 admin pages — gradient icon circles, titles, subtitles
- **New:** Modern pill-style tab navigation with dashicons and gradient active state
- **New:** Card-wrapped settings forms with improved inputs and branded submit buttons
- **Fixed:** Tab state lost after saving settings — now preserves active tab across form submissions

### v1.3.2 — 2026-02-23
- **New:** Branded admin bar on every plugin page — GitHub icon + repo link, author credit, version badge, "Star on GitHub" CTA
- **New:** Admin footer bar with GitHub, bug report, and releases links

### v1.3.1 — 2026-02-22
- **New:** Amazon affiliate disclosure integrated into all 8 showcase layouts (once per block)
- **New:** Display Settings toggle for disclosure visibility
- **Redesigned:** Analytics dashboard — modern card-based UI with gradient stat cards, visual bar chart, progress bars, rank badges
- **New:** Daily Average & Peak Day Clicks stats in analytics

### v1.3.0 — 2026-02-22
- **New:** 4 single-product showcase layouts — Hero, Compact, Split, Deal
- **New:** Visual Showcase Builder with WYSIWYG preview (admin preview = live post)
- **New:** Manual products CRUD system
- **Fixed:** Critical buy-button class mismatch causing unstyled CTA buttons
- **Improved:** All showcase CSS fully scoped to prevent theme/plugin conflicts
- **Improved:** Consistent responsive breakpoints (960px / 600px) across all layouts

### v1.0.0 — 2026-01-01
- Initial release — PA-API 5.0, 7 shortcodes, Gutenberg blocks, caching, geo-targeting, analytics

---

## 👤 Author

<table>
<tr>
<td>
  <strong>Numan</strong><br />
  <a href="https://github.com/numanrki">GitHub @numanrki</a><br />
  <a href="mailto:numanrki@gmail.com">numanrki@gmail.com</a>
</td>
</tr>
</table>

---

## 🐛 Bug Reports & Feature Requests

<table>
<tr>
<td>

**Found a bug?** &nbsp;·&nbsp; **Have a feature idea?** &nbsp;·&nbsp; **Something not working right?**

<br />

| Channel | How |
|:---|:---|
| 📧 **Email** | Send details to **[numanrki@gmail.com](mailto:numanrki@gmail.com)** — include your WP version, PHP version, and steps to reproduce. |
| 🐛 **GitHub Issues** | [Open an issue](https://github.com/numanrki/azonmate/issues/new) — great for tracking bugs publicly so others can benefit too. |

<br />

> **Are you a developer?** Pull requests are welcome! Fork the repo, make your changes, and submit a PR. Whether it's a one-line fix or a whole new feature — every contribution helps the community. See the [open issues](https://github.com/numanrki/azonmate/issues) for ideas on where to start.

</td>
</tr>
</table>

---

## 📄 License

<table>
<tr>
<td>

### AzonMate Free Use License v1.0

<br />

**✅ You CAN:**

| | |
|:---|:---|
| ✅ | **Download & install** on any number of your own WordPress sites |
| ✅ | **Use for any purpose** — personal blogs, business sites, client sites |
| ✅ | **Modify** the source code for your own needs |
| ✅ | **Share & redistribute** — give copies to others, post it on your blog, offer it for free download |

<br />

**❌ You CANNOT:**

| | |
|:---|:---|
| ❌ | **Sell** — no reselling, renting, or charging any fee for AzonMate |
| ❌ | **Sublicense** — you can't relicense it under different terms |
| ❌ | **Bundle in paid products** — don't include it in paid themes, plugins, or SaaS offerings |
| ❌ | **Remove credits** — the copyright notice and author attribution must stay |

<br />

> Full legal text → **[LICENSE](LICENSE)**

</td>
</tr>
</table>

---

<p align="center">
  <sub>Built with ☕ by <a href="https://github.com/numanrki"><strong>Numan</strong></a> — for the Amazon affiliate community.</sub><br />
  <sub>Questions? Reach out at <a href="mailto:numanrki@gmail.com">numanrki@gmail.com</a></sub>
</p>

<p align="center">
  <sub>If AzonMate saves you time or money, consider giving it a ⭐ — it helps others find it too.</sub>
</p>
