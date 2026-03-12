<p align="center">
  <img src="assets/images/icon.svg" width="90" alt="AzonMate" />
</p>

<h1 align="center">AzonMate</h1>

<p align="center">
  <strong>The Amazon Affiliate Product Engine for WordPress</strong>
</p>

<p align="center">
  <a href="https://github.com/numanrki/azonmate/releases/latest"><img src="https://img.shields.io/badge/version-2.2.0-ff9900?style=for-the-badge" alt="v2.2.0" /></a>&nbsp;
  <img src="https://img.shields.io/badge/WordPress-6.0%2B-21759b?style=for-the-badge&logo=wordpress&logoColor=white" alt="WordPress 6.0+" />&nbsp;
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777bb4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.1+" />&nbsp;
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

> [!IMPORTANT]
> **🔴 Amazon PA-API 5.0 is being deprecated on April 30, 2026.**
>
> Amazon is retiring the Product Advertising API (PA-API 5.0) and replacing it with the **Creators API**. Starting **v2.0.0**, AzonMate has fully migrated to the new Creators API with OAuth 2.0 authentication.
>
> **If you are upgrading from v1.x**, you must generate new **Creators API credentials** (Credential ID, Credential Secret, and Version) from your Amazon Associates account and enter them in **AzonMate → Settings → API**. Your old PA-API Access Key and Secret Key will no longer work after the deprecation date.
>
> 🔗 [Learn more about the Creators API migration](https://webservices.amazon.com/paapi5/documentation/)

<br />

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- What's New in v2.0.0 — Amazon Creators API                 -->
<!-- ═══════════════════════════════════════════════════════════ -->

<details>
<summary><strong>🆕 What's New in v2.0.0 — Amazon Creators API</strong></summary>

<br />

AzonMate v2.0.0 replaces the legacy PA-API 5.0 with Amazon's next-generation **Creators API**. Here's what changed under the hood:

| | PA-API 5.0 (old) | Creators API (v2.0.0) |
|:---|:---|:---|
| **Authentication** | AWS Signature v4 (HMAC-SHA256) | OAuth 2.0 Bearer token |
| **Credentials** | Access Key + Secret Key | Credential ID + Credential Secret + Version |
| **API Endpoint** | `webservices.amazon.*/paapi5/...` | `creatorsapi.amazon/catalog/v1/...` |
| **Token Caching** | None (signed per-request) | Auto-cached for 1 hour |
| **Request Format** | PascalCase params | lowerCamelCase params |
| **Offers Resource** | `Offers.Listings.*` | `offersV2.listings.*` |
| **Price Structure** | Flat (`Price.Amount`) | Nested (`price.money.amount`) |

**Key benefits of the Creators API:**
- **Simpler authentication** — no more complex AWS Signature v4 signing. Just a Bearer token.
- **Automatic token caching** — OAuth tokens are cached for ~1 hour, reducing overhead.
- **Single API host** — all marketplaces use `creatorsapi.amazon` (no per-region hosts).
- **Future-proof** — PA-API 5.0 shuts down April 30, 2026. Creators API is Amazon's long-term replacement.

**What's no longer available** (removed by Amazon):
- ⚠️ `CustomerReviews` (star rating, review count) — not available in Creators API
- ⚠️ `DeliveryInfo.IsPrimeEligible` (Prime badge) — not available in Creators API
- Existing manually-set ratings and Prime badges on your products will remain unchanged.

</details>

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
4. Head to **AzonMate → Settings**, enter your Amazon Creators API credentials, hit **Test Connection** — done.

That's it. Start dropping shortcodes or using the visual Showcase Builder.

---

## ✨ Features

<table>
<tr>
<td width="50%" valign="top">

### Amazon Creators API
- OAuth 2.0 authentication
- **22 marketplaces** — US, CA, MX, BR, UK, DE, FR, IT, ES, NL, BE, PL, SE, TR, SA, AE, EG, IE, IN, JP, SG, AU
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

### 8 Shortcodes
`box` · `link` · `image` · `field` · `list` · `table` · `showcase` · `collage`

### 8 Gutenberg Blocks
Product Box · Product List · Comparison Table · Bestseller · Text Link · Product Search · Showcase · Collage

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
Add products manually without an API key — full CRUD admin interface. **Fetch from Amazon** button auto-populates all fields from the Creators API.

### Product Collage
Dynamic multi-product collage shortcode with auto-adjusting grid layout and hover-reveal buy buttons.

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
[azonmate collage="ASIN1,ASIN2,ASIN3"]                        → Product collage
```

> **Tip:** Use the **Showcase Builder** (`AzonMate → Showcase` in the admin) to generate shortcodes visually — no syntax to memorize.

---

## ⚙️ Configuration

| Setting | Where |
|:---|:---|
| API credentials (Credential ID, Credential Secret, Version, Partner Tag, Marketplace) | **AzonMate → Settings → API** |
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
| Amazon Creators API | Credential ID, Secret & Version |

---

## 📂 Project Structure

```
azonmate/
├── azonmate.php                    → Main plugin bootstrap
├── uninstall.php                   → Clean uninstall handler
├── composer.json                   → Composer config (Creators API SDK)
├── assets/
│   ├── css/                        → Admin, public, editor, showcase, collage styles
│   ├── js/                         → Admin, public, click tracker, search modal, manual products
│   └── images/                     → Icons & SVG assets (icon, prime badge, star)
├── build/                          → Compiled Gutenberg blocks (8 blocks)
├── includes/
│   ├── class-plugin.php            → Core plugin orchestrator
│   ├── class-autoloader.php        → PSR-4 autoloader
│   ├── class-activator.php         → Activation hook handler
│   ├── class-deactivator.php       → Deactivation hook handler
│   ├── admin/                      → Settings, analytics, showcase builder, manual products, product search
│   │   └── views/                  → Admin page templates & partials
│   ├── api/                        → Amazon Creators API client & marketplace config
│   ├── blocks/                     → Block registrar & Gutenberg source (JSX)
│   │   └── src/                    → 8 block sources + shared React components
│   ├── cache/                      → Cache manager & cron refresh
│   ├── geo/                        → Geo-targeting & link rewriter
│   ├── models/                     → Product data model
│   ├── shortcodes/                 → 9 shortcode handlers + abstract base + manager
│   ├── templates/                  → Template renderer & helper functions
│   └── tracking/                   → Click tracker
├── templates/                      → Frontend templates (theme-overridable)
│   ├── showcase/                   → 8 showcase layout templates
│   ├── product-box/                → Box templates (default, horizontal, compact)
│   ├── comparison-table/           → Table template
│   ├── product-list/               → List templates (default, grid)
│   ├── bestseller/                 → Bestseller template
│   ├── collage/                    → Collage template
│   ├── image-link/                 → Image link template
│   └── text-link/                  → Text link template
├── vendor/                         → Composer dependencies (Creators API SDK, Guzzle)
└── languages/                      → Translation .pot file
```

---

## 📝 Changelog

> Full history in [CHANGELOG.md](CHANGELOG.md).

### v2.2.0 — 2026-03-12
- **Added:** 12 new Amazon marketplaces — MX, BR, NL, BE, PL, SE, TR, SA, AE, EG, IE, SG (total: 22 marketplaces across NA, EU, FE regions)
- **Fixed:** Shortcode fallback URL used marketplace code instead of domain — now resolves via `Marketplace::get_domain()`
- **Refactored:** LinkRewriter no longer maintains a hardcoded domain map — reads from the central Marketplace class

---

## 👤 Author

<table>
<tr>
<td>
  <strong>Numan Rashed</strong><br />
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
  <sub>Built with ☕ by <a href="https://github.com/numanrki"><strong>Numan Rashed</strong></a> — for the Amazon affiliate community.</sub><br />
  <sub>Questions? Reach out at <a href="mailto:numanrki@gmail.com">numanrki@gmail.com</a></sub>
</p>

<p align="center">
  <sub>If AzonMate saves you time or money, consider giving it a ⭐ — it helps others find it too.</sub>
</p>
