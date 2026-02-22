<?php
/**
 * Affiliate link URL rewriting.
 *
 * @package AzonMate\Geo
 * @since   1.0.0
 */

namespace AzonMate\Geo;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LinkRewriter
 *
 * Rewrites Amazon product URLs based on the visitor's detected country.
 *
 * @since 1.0.0
 */
class LinkRewriter {

	/**
	 * Geo-targeting instance.
	 *
	 * @since 1.0.0
	 * @var GeoTargeting
	 */
	private $geo;

	/**
	 * Amazon domain map (marketplace code ⇒ domain).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $domains = array(
		'www'    => 'www.amazon.com',
		'uk'     => 'www.amazon.co.uk',
		'de'     => 'www.amazon.de',
		'fr'     => 'www.amazon.fr',
		'in'     => 'www.amazon.in',
		'ca'     => 'www.amazon.ca',
		'jp'     => 'www.amazon.co.jp',
		'it'     => 'www.amazon.it',
		'es'     => 'www.amazon.es',
		'au'     => 'www.amazon.com.au',
	);

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param GeoTargeting $geo Geo-targeting instance.
	 */
	public function __construct( GeoTargeting $geo ) {
		$this->geo = $geo;
	}

	/**
	 * Build an affiliate product URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin        Product ASIN.
	 * @param string $marketplace Marketplace code (optional, auto-detected if empty).
	 * @param string $partner_tag Partner tag (optional, auto-detected if empty).
	 * @return string Full affiliate URL.
	 */
	public function get_product_url( $asin, $marketplace = '', $partner_tag = '' ) {
		if ( empty( $marketplace ) ) {
			$marketplace = $this->geo->get_marketplace();
		}

		if ( empty( $partner_tag ) ) {
			$partner_tag = $this->geo->get_partner_tag();
		}

		$domain = $this->domains[ $marketplace ] ?? $this->domains['www'];

		return sprintf(
			'https://%s/dp/%s?tag=%s&linkCode=ogi&th=1&psc=1',
			$domain,
			rawurlencode( $asin ),
			rawurlencode( $partner_tag )
		);
	}

	/**
	 * Rewrite an existing Amazon URL for the visitor's locale.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Original Amazon URL.
	 * @return string Rewritten URL.
	 */
	public function rewrite_url( $url ) {
		// Extract ASIN from the URL.
		$asin = $this->extract_asin( $url );

		if ( ! $asin ) {
			return $url;
		}

		return $this->get_product_url( $asin );
	}

	/**
	 * Extract an ASIN from an Amazon URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Amazon URL.
	 * @return string|null ASIN or null.
	 */
	public function extract_asin( $url ) {
		// Matches /dp/ASIN, /gp/product/ASIN, /exec/obidos/asin/ASIN.
		if ( preg_match( '#(?:/dp/|/gp/product/|/exec/obidos/asin/)([A-Z0-9]{10})#i', $url, $matches ) ) {
			return strtoupper( $matches[1] );
		}

		return null;
	}
}
