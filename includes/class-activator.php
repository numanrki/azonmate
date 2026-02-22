<?php
/**
 * Plugin activation handler.
 *
 * @package AzonMate
 * @since   1.0.0
 */

namespace AzonMate;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 *
 * Handles all tasks that need to run when the plugin is activated:
 * - Create custom database tables
 * - Set default options
 * - Schedule cron events
 *
 * @since 1.0.0
 */
class Activator {

	/**
	 * Run activation tasks.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		self::create_tables();
		self::set_default_options();
		self::schedule_cron_events();
		self::store_version();

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Create custom database tables.
	 *
	 * @since 1.0.0
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Products cache table.
		$products_table = $wpdb->prefix . 'azonmate_products';
		$products_sql   = "CREATE TABLE {$products_table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			asin VARCHAR(20) NOT NULL,
			marketplace VARCHAR(10) NOT NULL DEFAULT 'US',
			title TEXT,
			url TEXT,
			image_small VARCHAR(500),
			image_medium VARCHAR(500),
			image_large VARCHAR(500),
			price_display VARCHAR(50),
			price_amount DECIMAL(10,2),
			price_currency VARCHAR(10),
			list_price_amount DECIMAL(10,2),
			savings_percentage INT,
			rating DECIMAL(2,1),
			review_count INT,
			is_prime TINYINT(1) DEFAULT 0,
			availability VARCHAR(100),
			brand VARCHAR(255),
			features TEXT,
			description TEXT,
			browse_node VARCHAR(255),
			is_manual TINYINT(1) DEFAULT 0,
			badge_label VARCHAR(100) DEFAULT '',
			button_text VARCHAR(100) DEFAULT '',
			last_updated DATETIME NOT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY asin_marketplace (asin, marketplace),
			KEY idx_last_updated (last_updated),
			KEY idx_is_manual (is_manual)
		) {$charset_collate};";

		// Clicks tracking table.
		$clicks_table = $wpdb->prefix . 'azonmate_clicks';
		$clicks_sql   = "CREATE TABLE {$clicks_table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			asin VARCHAR(20) NOT NULL,
			post_id BIGINT(20) UNSIGNED,
			country VARCHAR(5),
			ip_hash VARCHAR(64),
			clicked_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY idx_asin (asin),
			KEY idx_post_id (post_id),
			KEY idx_clicked_at (clicked_at)
		) {$charset_collate};";

		// Comparison tables.
		$comparison_table = $wpdb->prefix . 'azonmate_comparison_tables';
		$comparison_sql   = "CREATE TABLE {$comparison_table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			asins TEXT NOT NULL,
			columns TEXT,
			highlight_asin VARCHAR(20),
			settings TEXT,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $products_sql );
		dbDelta( $clicks_sql );
		dbDelta( $comparison_sql );
	}

	/**
	 * Set default plugin options.
	 *
	 * @since 1.0.0
	 */
	private static function set_default_options() {
		$defaults = array(
			// API settings.
			'azon_mate_access_key'          => '',
			'azon_mate_secret_key'          => '',
			'azon_mate_partner_tag'         => '',
			'azon_mate_marketplace'         => 'US',

			// Display settings.
			'azon_mate_default_template'    => 'default',
			'azon_mate_show_prices'         => '1',
			'azon_mate_show_ratings'        => '1',
			'azon_mate_show_prime_badge'    => '1',
			'azon_mate_show_description'    => '1',
			'azon_mate_show_buy_button'     => '1',
			'azon_mate_buy_button_text'     => 'Buy on Amazon',
			'azon_mate_buy_button_color'    => '#FF9900',
			'azon_mate_open_new_tab'        => '1',
			'azon_mate_nofollow_links'      => '1',
			'azon_mate_custom_css'          => '',

			// Cache settings.
			'azon_mate_cache_enabled'       => '1',
			'azon_mate_cache_duration'      => 24,

			// Geo-targeting settings.
			'azon_mate_geo_enabled'         => '0',
			'azon_mate_geo_tags'            => array(),
			'azon_mate_geo_fallback'        => 'US',

			// Tracking settings.
			'azon_mate_tracking_enabled'    => '1',

			// Showcase settings.
			'azon_mate_showcase_layout'     => 'grid',
			'azon_mate_showcase_columns'    => '3',

			// Advanced settings.
			'azon_mate_disable_css'         => '0',
			'azon_mate_api_throttle'        => 1,
			'azon_mate_debug_mode'          => '0',
			'azon_mate_uninstall_delete'    => '0',

			// Disclaimer.
			'azon_mate_show_disclaimer'     => '1',
			'azon_mate_disclaimer_text'     => 'As an Amazon Associate, I earn from qualifying purchases.',
			'azon_mate_disclaimer_position' => 'footer',
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( $key ) ) {
				update_option( $key, $value );
			}
		}
	}

	/**
	 * Schedule WP-Cron events.
	 *
	 * @since 1.0.0
	 */
	private static function schedule_cron_events() {
		if ( ! wp_next_scheduled( 'azon_mate_refresh_cache' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'azon_mate_refresh_cache' );
		}

		if ( ! wp_next_scheduled( 'azon_mate_cleanup_clicks' ) ) {
			wp_schedule_event( time(), 'daily', 'azon_mate_cleanup_clicks' );
		}
	}

	/**
	 * Store the current plugin version in the database.
	 *
	 * @since 1.0.0
	 */
	private static function store_version() {
		update_option( 'azonmate_version', AZON_MATE_VERSION );
	}
}
