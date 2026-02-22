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
 * Provides marketplace-specific configuration including hostnames,
 * regions, and endpoints for the Amazon PA-API 5.0.
 *
 * @since 1.0.0
 */
class Marketplace {

	/**
	 * Supported marketplaces with their configuration.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $marketplaces = array(
		'US' => array(
			'host'   => 'webservices.amazon.com',
			'region' => 'us-east-1',
			'label'  => 'United States',
			'domain' => 'amazon.com',
			'tld'    => 'com',
		),
		'UK' => array(
			'host'   => 'webservices.amazon.co.uk',
			'region' => 'eu-west-1',
			'label'  => 'United Kingdom',
			'domain' => 'amazon.co.uk',
			'tld'    => 'co.uk',
		),
		'DE' => array(
			'host'   => 'webservices.amazon.de',
			'region' => 'eu-west-1',
			'label'  => 'Germany',
			'domain' => 'amazon.de',
			'tld'    => 'de',
		),
		'FR' => array(
			'host'   => 'webservices.amazon.fr',
			'region' => 'eu-west-1',
			'label'  => 'France',
			'domain' => 'amazon.fr',
			'tld'    => 'fr',
		),
		'IN' => array(
			'host'   => 'webservices.amazon.in',
			'region' => 'eu-west-1',
			'label'  => 'India',
			'domain' => 'amazon.in',
			'tld'    => 'in',
		),
		'CA' => array(
			'host'   => 'webservices.amazon.ca',
			'region' => 'us-east-1',
			'label'  => 'Canada',
			'domain' => 'amazon.ca',
			'tld'    => 'ca',
		),
		'JP' => array(
			'host'   => 'webservices.amazon.co.jp',
			'region' => 'us-west-2',
			'label'  => 'Japan',
			'domain' => 'amazon.co.jp',
			'tld'    => 'co.jp',
		),
		'IT' => array(
			'host'   => 'webservices.amazon.it',
			'region' => 'eu-west-1',
			'label'  => 'Italy',
			'domain' => 'amazon.it',
			'tld'    => 'it',
		),
		'ES' => array(
			'host'   => 'webservices.amazon.es',
			'region' => 'eu-west-1',
			'label'  => 'Spain',
			'domain' => 'amazon.es',
			'tld'    => 'es',
		),
		'AU' => array(
			'host'   => 'webservices.amazon.com.au',
			'region' => 'us-west-2',
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
	 * Get the API host for a marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Marketplace code.
	 * @return string
	 */
	public static function get_host( $code ) {
		$marketplace = self::get( $code );
		return $marketplace ? $marketplace['host'] : 'webservices.amazon.com';
	}

	/**
	 * Get the AWS region for a marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Marketplace code.
	 * @return string
	 */
	public static function get_region( $code ) {
		$marketplace = self::get( $code );
		return $marketplace ? $marketplace['region'] : 'us-east-1';
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
	 * @param string $marketplace Marketplace code.
	 * @param string $operation   API operation (searchitems, getitems, getbrowsenodes).
	 * @return string
	 */
	public static function get_endpoint( $marketplace, $operation ) {
		$host = self::get_host( $marketplace );
		return sprintf( 'https://%s/paapi5/%s', $host, strtolower( $operation ) );
	}

	/**
	 * Get the X-Amz-Target header value for an operation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $operation The API operation name.
	 * @return string
	 */
	public static function get_amz_target( $operation ) {
		return 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.' . $operation;
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
