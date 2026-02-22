<?php
/**
 * Geo-targeting for country detection and affiliate tag swapping.
 *
 * @package AzonMate\Geo
 * @since   1.0.0
 */

namespace AzonMate\Geo;

use AzonMate\API\Marketplace;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GeoTargeting
 *
 * Detects the visitor's country and provides the appropriate
 * affiliate tag and marketplace based on their location.
 *
 * @since 1.0.0
 */
class GeoTargeting {

	/**
	 * Cookie name for storing detected country.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const COOKIE_NAME = 'azonmate_country';

	/**
	 * Cookie expiration in seconds (30 days).
	 *
	 * @since 1.0.0
	 * @var int
	 */
	const COOKIE_EXPIRY = 2592000;

	/**
	 * The detected country code.
	 *
	 * @since 1.0.0
	 * @var string|null
	 */
	private $country = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_azon_mate_detect_country', array( $this, 'ajax_detect_country' ) );
		add_action( 'wp_ajax_nopriv_azon_mate_detect_country', array( $this, 'ajax_detect_country' ) );
	}

	/**
	 * Detect the visitor's country.
	 *
	 * Checks in order:
	 * 1. Cookie (if previously detected)
	 * 2. CloudFlare header
	 * 3. Free IP geolocation API
	 *
	 * @since 1.0.0
	 *
	 * @return string Two-letter country code or empty string.
	 */
	public function detect_country() {
		if ( null !== $this->country ) {
			return $this->country;
		}

		// 1. Check cookie.
		if ( isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			$this->country = sanitize_key( $_COOKIE[ self::COOKIE_NAME ] );
			return $this->country;
		}

		// 2. Check CloudFlare header.
		if ( isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
			$this->country = strtoupper( sanitize_key( $_SERVER['HTTP_CF_IPCOUNTRY'] ) );
			$this->set_country_cookie( $this->country );
			return $this->country;
		}

		// 3. Try IP-based geolocation.
		$ip = $this->get_visitor_ip();
		if ( ! empty( $ip ) ) {
			$this->country = $this->geolocate_ip( $ip );
			if ( ! empty( $this->country ) ) {
				$this->set_country_cookie( $this->country );
			}
		}

		if ( empty( $this->country ) ) {
			$this->country = '';
		}

		return $this->country;
	}

	/**
	 * Get the appropriate affiliate tag for the visitor's country.
	 *
	 * @since 1.0.0
	 *
	 * @return string Affiliate partner tag.
	 */
	public function get_partner_tag() {
		$country = $this->detect_country();

		if ( empty( $country ) ) {
			return get_option( 'azon_mate_partner_tag', '' );
		}

		$geo_tags    = get_option( 'azon_mate_geo_tags', array() );
		$marketplace = Marketplace::country_to_marketplace( $country );

		if ( ! empty( $geo_tags[ $marketplace ] ) ) {
			return $geo_tags[ $marketplace ];
		}

		return get_option( 'azon_mate_partner_tag', '' );
	}

	/**
	 * Get the best marketplace for the visitor's country.
	 *
	 * @since 1.0.0
	 *
	 * @return string Marketplace code.
	 */
	public function get_marketplace() {
		$country = $this->detect_country();

		if ( empty( $country ) ) {
			return get_option( 'azon_mate_marketplace', 'US' );
		}

		return Marketplace::country_to_marketplace( $country );
	}

	/**
	 * AJAX: Detect country (for client-side geo-targeting).
	 *
	 * @since 1.0.0
	 */
	public function ajax_detect_country() {
		$country     = $this->detect_country();
		$marketplace = $this->get_marketplace();
		$partner_tag = $this->get_partner_tag();

		wp_send_json_success( array(
			'country'     => $country,
			'marketplace' => $marketplace,
			'partnerTag'  => $partner_tag,
			'domain'      => Marketplace::get_domain( $marketplace ),
		) );
	}

	/**
	 * Get the visitor's IP address.
	 *
	 * @since 1.0.0
	 *
	 * @return string IP address.
	 */
	private function get_visitor_ip() {
		$headers = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);

		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );
				// Handle comma-separated list (X-Forwarded-For).
				if ( strpos( $ip, ',' ) !== false ) {
					$ip = trim( explode( ',', $ip )[0] );
				}
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					return $ip;
				}
			}
		}

		return '';
	}

	/**
	 * Geolocate an IP address using a free API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $ip IP address.
	 * @return string Two-letter country code or empty string.
	 */
	private function geolocate_ip( $ip ) {
		// Check transient cache first.
		$cache_key = 'azon_mate_geo_' . md5( $ip );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Use ip-api.com (free, no API key required).
		$response = wp_remote_get(
			'http://ip-api.com/json/' . urlencode( $ip ) . '?fields=countryCode',
			array( 'timeout' => 5 )
		);

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['countryCode'] ) ) {
			$country = strtoupper( sanitize_key( $body['countryCode'] ) );
			// Cache for 24 hours.
			set_transient( $cache_key, $country, DAY_IN_SECONDS );
			return $country;
		}

		return '';
	}

	/**
	 * Set the country detection cookie.
	 *
	 * @since 1.0.0
	 *
	 * @param string $country Country code.
	 */
	private function set_country_cookie( $country ) {
		if ( ! headers_sent() ) {
			setcookie(
				self::COOKIE_NAME,
				sanitize_key( $country ),
				time() + self::COOKIE_EXPIRY,
				COOKIEPATH,
				COOKIE_DOMAIN,
				is_ssl(),
				true
			);
		}
	}
}
