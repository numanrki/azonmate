# Changelog

All notable changes to AzonMate will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-01

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
