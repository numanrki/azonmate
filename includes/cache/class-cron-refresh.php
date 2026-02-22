<?php
/**
 * WP-Cron scheduled cache refresh.
 *
 * @package AzonMate\Cache
 * @since   1.0.0
 */

namespace AzonMate\Cache;

use AzonMate\API\AmazonAPI;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CronRefresh
 *
 * Handles scheduled background refresh of stale product data.
 *
 * @since 1.0.0
 */
class CronRefresh {

	/**
	 * Amazon API instance.
	 *
	 * @since 1.0.0
	 * @var AmazonAPI
	 */
	private $api;

	/**
	 * Cache manager instance.
	 *
	 * @since 1.0.0
	 * @var CacheManager
	 */
	private $cache;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param AmazonAPI    $api   Amazon API instance.
	 * @param CacheManager $cache Cache manager instance.
	 */
	public function __construct( AmazonAPI $api, CacheManager $cache ) {
		$this->api   = $api;
		$this->cache = $cache;

		// Hook into WP-Cron events.
		add_action( 'azon_mate_refresh_cache', array( $this, 'refresh_stale_products' ) );
		add_action( 'azon_mate_cleanup_clicks', array( $this, 'cleanup_old_clicks' ) );
	}

	/**
	 * Refresh stale product data in batches.
	 *
	 * Fetches stale products from the cache and refreshes them
	 * in batches of 10 (API limit per GetItems call).
	 *
	 * @since 1.0.0
	 */
	public function refresh_stale_products() {
		$stale_products = $this->cache->get_stale_products( 50 );

		if ( empty( $stale_products ) ) {
			return;
		}

		// Group by marketplace.
		$grouped = array();
		foreach ( $stale_products as $item ) {
			$grouped[ $item->marketplace ][] = $item->asin;
		}

		// Refresh each marketplace group in batches of 10.
		foreach ( $grouped as $marketplace => $asins ) {
			$batches = array_chunk( $asins, 10 );

			foreach ( $batches as $batch ) {
				$result = $this->api->get_items( $batch, $marketplace, true );

				if ( is_wp_error( $result ) ) {
					if ( \AzonMate\Plugin::is_debug_enabled() ) {
						error_log( '[AzonMate] Cron refresh error: ' . $result->get_error_message() );
					}
					// Don't continue if API is failing.
					break 2;
				}

				// Rate limiting is handled inside the API client.
			}
		}

		if ( \AzonMate\Plugin::is_debug_enabled() ) {
			error_log( sprintf( '[AzonMate] Cron: Refreshed %d stale products.', count( $stale_products ) ) );
		}
	}

	/**
	 * Clean up old click tracking data.
	 *
	 * Removes click records older than the configured retention period.
	 *
	 * @since 1.0.0
	 */
	public function cleanup_old_clicks() {
		global $wpdb;

		$retention_days = absint( apply_filters( 'azon_mate_click_retention_days', 90 ) );
		$cutoff_date    = gmdate( 'Y-m-d H:i:s', time() - ( $retention_days * DAY_IN_SECONDS ) );

		$clicks_table = $wpdb->prefix . 'azonmate_clicks';

		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$clicks_table} WHERE clicked_at < %s",
				$cutoff_date
			)
		);

		if ( \AzonMate\Plugin::is_debug_enabled() ) {
			error_log( sprintf( '[AzonMate] Cron: Cleaned up %d old click records.', $deleted ) );
		}
	}
}
