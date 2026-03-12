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
 * and domains for the Amazon Creators API.
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
		// — NA Region (Credential Version 2.1 / 3.1) —
		'US' => array(
			'region' => 'NA',
			'label'  => 'United States',
			'domain' => 'amazon.com',
			'tld'    => 'com',
		),
		'CA' => array(
			'region' => 'NA',
			'label'  => 'Canada',
			'domain' => 'amazon.ca',
			'tld'    => 'ca',
		),
		'MX' => array(
			'region' => 'NA',
			'label'  => 'Mexico',
			'domain' => 'amazon.com.mx',
			'tld'    => 'com.mx',
		),
		'BR' => array(
			'region' => 'NA',
			'label'  => 'Brazil',
			'domain' => 'amazon.com.br',
			'tld'    => 'com.br',
		),
		// — EU Region (Credential Version 2.2 / 3.2) —
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
		'NL' => array(
			'region' => 'EU',
			'label'  => 'Netherlands',
			'domain' => 'amazon.nl',
			'tld'    => 'nl',
		),
		'BE' => array(
			'region' => 'EU',
			'label'  => 'Belgium',
			'domain' => 'amazon.com.be',
			'tld'    => 'com.be',
		),
		'PL' => array(
			'region' => 'EU',
			'label'  => 'Poland',
			'domain' => 'amazon.pl',
			'tld'    => 'pl',
		),
		'SE' => array(
			'region' => 'EU',
			'label'  => 'Sweden',
			'domain' => 'amazon.se',
			'tld'    => 'se',
		),
		'TR' => array(
			'region' => 'EU',
			'label'  => 'Turkey',
			'domain' => 'amazon.com.tr',
			'tld'    => 'com.tr',
		),
		'SA' => array(
			'region' => 'EU',
			'label'  => 'Saudi Arabia',
			'domain' => 'amazon.sa',
			'tld'    => 'sa',
		),
		'AE' => array(
			'region' => 'EU',
			'label'  => 'United Arab Emirates',
			'domain' => 'amazon.ae',
			'tld'    => 'ae',
		),
		'EG' => array(
			'region' => 'EU',
			'label'  => 'Egypt',
			'domain' => 'amazon.eg',
			'tld'    => 'eg',
		),
		'IE' => array(
			'region' => 'EU',
			'label'  => 'Ireland',
			'domain' => 'amazon.ie',
			'tld'    => 'ie',
		),
		'IN' => array(
			'region' => 'EU',
			'label'  => 'India',
			'domain' => 'amazon.in',
			'tld'    => 'in',
		),
		// — FE Region (Credential Version 2.3 / 3.3) —
		'JP' => array(
			'region' => 'FE',
			'label'  => 'Japan',
			'domain' => 'amazon.co.jp',
			'tld'    => 'co.jp',
		),
		'SG' => array(
			'region' => 'FE',
			'label'  => 'Singapore',
			'domain' => 'amazon.sg',
			'tld'    => 'sg',
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
	 * Map a country code (ISO 3166-1 alpha-2) to the closest marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $country_code Two-letter country code.
	 * @return string Marketplace code.
	 */
	public static function country_to_marketplace( $country_code ) {
		$map = array(
			// NA
			'US' => 'US',
			'CA' => 'CA',
			'MX' => 'MX',
			'BR' => 'BR',
			// EU
			'GB' => 'UK',
			'DE' => 'DE',
			'AT' => 'DE',
			'CH' => 'DE',
			'FR' => 'FR',
			'IT' => 'IT',
			'ES' => 'ES',
			'PT' => 'ES',
			'NL' => 'NL',
			'BE' => 'BE',
			'PL' => 'PL',
			'SE' => 'SE',
			'TR' => 'TR',
			'SA' => 'SA',
			'AE' => 'AE',
			'EG' => 'EG',
			'IE' => 'IE',
			'IN' => 'IN',
			// FE
			'JP' => 'JP',
			'SG' => 'SG',
			'AU' => 'AU',
			'NZ' => 'AU',
		);

		$country_code = strtoupper( $country_code );
		return isset( $map[ $country_code ] ) ? $map[ $country_code ] : get_option( 'azon_mate_geo_fallback', 'US' );
	}
}
