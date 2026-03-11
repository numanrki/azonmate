<?php
/**
 * Amazon Marketplace configuration.
 *
 * @package AzonMate\API
 * @since   1.0.0
 */

namespace AzonMate\API;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Marketplace
 *
 * Provides marketplace-specific configuration including regions
 * and endpoints for the Amazon Creators API.
 *
 * @since 1.0.0
 */
class Marketplace {

	/**
	 * Single API host for all marketplaces.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const API_HOST = 'creatorsapi.amazon';

	/**
	 * Regional token endpoints for v2.x (Cognito) credentials.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private static $cognito_endpoints = array(
		'NA' => 'creatorsapi.auth.us-east-1.amazoncognito.com',
		'EU' => 'creatorsapi.auth.eu-south-2.amazoncognito.com',
		'FE' => 'creatorsapi.auth.us-west-2.amazoncognito.com',
	);

	/**
	 * Regional token endpoints for v3.x (LwA) credentials.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private static $lwa_endpoints = array(
		'NA' => 'api.amazon.com',
		'EU' => 'api.amazon.co.uk',
		'FE' => 'api.amazon.co.jp',
	);

	/**
	 * Supported marketplaces with their configuration.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $marketplaces = array(
		'US' => array(
			'region' => 'NA',
			'label'  => 'United States',
			'domain' => 'amazon.com',
			'tld'    => 'com',
		),
		'UK' => array(
			'region' => 'EU',
			'label'  => 'United Kingdom',
			'domain' => 'amazon.co.uk',
			'tld'    => 'co.uk',
		),
		'DE' => array(
			'region' => 'EU',
			'label'  => 'Germany',
			'domain' => 'amazon.de',
			'tld'    => 'de',
		),
		'FR' => array(
			'region' => 'EU',
			'label'  => 'France',
			'domain' => 'amazon.fr',
			'tld'    => 'fr',
		),
		'IN' => array(
			'region' => 'EU',
			'label'  => 'India',
			'domain' => 'amazon.in',
			'tld'    => 'in',
		),
		'CA' => array(
			'region' => 'NA',
			'label'  => 'Canada',
			'domain' => 'amazon.ca',
			'tld'    => 'ca',
		),
		'JP' => array(
			'region' => 'FE',
			'label'  => 'Japan',
			'domain' => 'amazon.co.jp',
			'tld'    => 'co.jp',
		),
		'IT' => array(
			'region' => 'EU',
			'label'  => 'Italy',
			'domain' => 'amazon.it',
			'tld'    => 'it',
		),
		'ES' => array(
			'region' => 'EU',
			'label'  => 'Spain',
			'domain' => 'amazon.es',
			'tld'    => 'es',
		),
		'AU' => array(
			'region' => 'FE',
			'label'  => 'Australia',
			'domain' => 'amazon.com.au',
			'tld'    => 'com.au',
		),
	);

	/**
	 * Amazon product search categories.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $categories = array(
		'All'                  => 'All Departments',
		'Appliances'           => 'Appliances',
		'ArtsAndCrafts'        => 'Arts & Crafts',
		'Automotive'           => 'Automotive',
		'Baby'                 => 'Baby',
		'Beauty'               => 'Beauty',
		'Books'                => 'Books',
		'Computers'            => 'Computers',
		'DigitalMusic'         => 'Digital Music',
		'Electronics'          => 'Electronics',
		'Fashion'              => 'Fashion',
		'GardenAndOutdoor'     => 'Garden & Outdoor',
		'GiftCards'            => 'Gift Cards',
		'GroceryAndGourmetFood' => 'Grocery',
		'Handmade'             => 'Handmade',
		'HealthPersonalCare'   => 'Health & Personal Care',
		'HomeAndKitchen'       => 'Home & Kitchen',
		'KindleStore'          => 'Kindle Store',
		'Luggage'              => 'Luggage',
		'MoviesAndTV'          => 'Movies & TV',
		'Music'                => 'Music',
		'OfficeProducts'       => 'Office Products',
		'PetSupplies'          => 'Pet Supplies',
		'Software'             => 'Software',
		'SportsAndOutdoors'    => 'Sports & Outdoors',
		'ToolsAndHomeImprovement' => 'Tools & Home Improvement',
		'ToysAndGames'         => 'Toys & Games',
		'VideoGames'           => 'Video Games',
	);

	/**
	 * Get the full configuration for a marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Marketplace code (US, UK, DE, etc.).
	 * @return array|null Marketplace config or null if not found.
	 */
	public static function get( $code ) {
		$code = strtoupper( sanitize_text_field( $code ) );
		return isset( self::$marketplaces[ $code ] ) ? self::$marketplaces[ $code ] : null;
	}

	/**
	 * Get the API region for a marketplace (NA, EU, or FE).
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Marketplace code.
	 * @return string
	 */
	public static function get_region( $code ) {
		$marketplace = self::get( $code );
		return $marketplace ? $marketplace['region'] : 'NA';
	}

	/**
	 * Get the Amazon domain for a marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Marketplace code.
	 * @return string
	 */
	public static function get_domain( $code ) {
		$marketplace = self::get( $code );
		return $marketplace ? $marketplace['domain'] : 'amazon.com';
	}

	/**
	 * Get the display label for a marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Marketplace code.
	 * @return string
	 */
	public static function get_label( $code ) {
		$marketplace = self::get( $code );
		return $marketplace ? $marketplace['label'] : '';
	}

	/**
	 * Get all marketplace codes and labels for dropdowns.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of code => label.
	 */
	public static function get_all_options() {
		$options = array();
		foreach ( self::$marketplaces as $code => $config ) {
			$options[ $code ] = $config['label'] . ' (' . $config['domain'] . ')';
		}
		return $options;
	}

	/**
	 * Get all supported marketplace codes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_all_codes() {
		return array_keys( self::$marketplaces );
	}

	/**
	 * Check if a marketplace code is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Marketplace code.
	 * @return bool
	 */
	public static function is_valid( $code ) {
		return isset( self::$marketplaces[ strtoupper( $code ) ] );
	}

	/**
	 * Get all product categories for search filters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of category_id => label.
	 */
	public static function get_categories() {
		return self::$categories;
	}

	/**
	 * Build the full API endpoint URL for an operation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $marketplace Marketplace code (unused — single host for all marketplaces).
	 * @param string $operation   API operation (searchItems, getItems, getBrowseNodes, getVariations).
	 * @return string
	 */
	public static function get_endpoint( $marketplace, $operation ) {
		return sprintf( 'https://%s/catalog/v1/%s', self::API_HOST, $operation );
	}

	/**
	 * Get the OAuth token endpoint for the given credential version.
	 *
	 * @since 2.0.0
	 *
	 * @param string $version Credential version (2.1, 2.2, 2.3, 3.1, 3.2, 3.3).
	 * @return string Full token endpoint URL.
	 */
	public static function get_token_endpoint( $version ) {
		$major  = (int) $version;
		$region = self::version_to_region( $version );

		if ( 3 === $major ) {
			$host = isset( self::$lwa_endpoints[ $region ] ) ? self::$lwa_endpoints[ $region ] : self::$lwa_endpoints['NA'];
			return 'https://' . $host . '/auth/o2/token';
		}

		// Default to v2.x (Cognito).
		$host = isset( self::$cognito_endpoints[ $region ] ) ? self::$cognito_endpoints[ $region ] : self::$cognito_endpoints['NA'];
		return 'https://' . $host . '/oauth2/token';
	}

	/**
	 * Map a credential version to its API region.
	 *
	 * @since 2.0.0
	 *
	 * @param string $version Credential version.
	 * @return string Region code (NA, EU, FE).
	 */
	public static function version_to_region( $version ) {
		$map = array(
			'2.1' => 'NA',
			'2.2' => 'EU',
			'2.3' => 'FE',
			'3.1' => 'NA',
			'3.2' => 'EU',
			'3.3' => 'FE',
		);
		return isset( $map[ $version ] ) ? $map[ $version ] : 'NA';
	}

	/**
	 * Map a country code (ISO 3166-1 alpha-2) to the closest marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $country_code Two-letter country code.
	 * @return string Marketplace code.
	 */
	public static function country_to_marketplace( $country_code ) {
		$map = array(
			'US' => 'US',
			'GB' => 'UK',
			'DE' => 'DE',
			'AT' => 'DE',
			'CH' => 'DE',
			'FR' => 'FR',
			'BE' => 'FR',
			'IN' => 'IN',
			'CA' => 'CA',
			'JP' => 'JP',
			'IT' => 'IT',
			'ES' => 'ES',
			'PT' => 'ES',
			'AU' => 'AU',
			'NZ' => 'AU',
		);

		$country_code = strtoupper( $country_code );
		return isset( $map[ $country_code ] ) ? $map[ $country_code ] : get_option( 'azon_mate_geo_fallback', 'US' );
	}
}
