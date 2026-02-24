<?php
/**
 * Cache manager for product data.
 *
 * @package AzonMate\Cache
 * @since   1.0.0
 */

namespace AzonMate\Cache;

use AzonMate\Models\Product;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CacheManager
 *
 * Manages product data caching using a custom database table
 * and WordPress transients for fast retrieval.
 *
 * @since 1.0.0
 */
class CacheManager {

	/**
	 * The products table name (with prefix).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $table;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'azonmate_products';
	}

	/**
	 * Save a product to the cache (DB + transient).
	 *
	 * @since 1.0.0
	 *
	 * @param Product $product The product to cache.
	 * @return bool True on success.
	 */
	public function save_product( Product $product ) {
		global $wpdb;

		if ( ! $product->is_valid() ) {
			return false;
		}

		$data = $product->to_db_array();

		// Upsert: insert or update on duplicate key.
		$existing = $this->get_product_row( $product->get_asin(), $product->get_marketplace() );

		if ( $existing ) {
			// Update existing record.
			$result = $wpdb->update(
				$this->table,
				array(
					'title'              => $data['title'],
					'url'                => $data['url'],
					'image_small'        => $data['image_small'],
					'image_medium'       => $data['image_medium'],
					'image_large'        => $data['image_large'],
					'price_display'      => $data['price_display'],
					'price_amount'       => $data['price_amount'],
					'price_currency'     => $data['price_currency'],
					'list_price_amount'  => $data['list_price_amount'],
					'savings_percentage' => $data['savings_percentage'],
					'rating'             => $data['rating'],
					'review_count'       => $data['review_count'],
					'is_prime'           => $data['is_prime'],
					'availability'       => $data['availability'],
					'brand'              => $data['brand'],
					'features'           => $data['features'],
					'description'        => $data['description'],
					'browse_node'        => $data['browse_node'],
					'is_manual'          => $data['is_manual'],
					'last_updated'       => current_time( 'mysql' ),
				),
				array(
					'asin'        => $product->get_asin(),
					'marketplace' => $product->get_marketplace(),
				),
				array(
					'%s', '%s', '%s', '%s', '%s',
					'%s', '%f', '%s', '%f', '%d',
					'%f', '%d', '%d', '%s', '%s',
					'%s', '%s', '%s', '%d', '%s',
				),
				array( '%s', '%s' )
			);
		} else {
			// Insert new record.
			$result = $wpdb->insert(
				$this->table,
				array(
					'asin'               => $data['asin'],
					'marketplace'        => $data['marketplace'],
					'title'              => $data['title'],
					'url'                => $data['url'],
					'image_small'        => $data['image_small'],
					'image_medium'       => $data['image_medium'],
					'image_large'        => $data['image_large'],
					'price_display'      => $data['price_display'],
					'price_amount'       => $data['price_amount'],
					'price_currency'     => $data['price_currency'],
					'list_price_amount'  => $data['list_price_amount'],
					'savings_percentage' => $data['savings_percentage'],
					'rating'             => $data['rating'],
					'review_count'       => $data['review_count'],
					'is_prime'           => $data['is_prime'],
					'availability'       => $data['availability'],
					'brand'              => $data['brand'],
					'features'           => $data['features'],
					'description'        => $data['description'],
					'browse_node'        => $data['browse_node'],
					'is_manual'          => $data['is_manual'],
					'last_updated'       => current_time( 'mysql' ),
					'created_at'         => current_time( 'mysql' ),
				),
				array(
					'%s', '%s', '%s', '%s', '%s', '%s', '%s',
					'%s', '%f', '%s', '%f', '%d',
					'%f', '%d', '%d', '%s', '%s',
					'%s', '%s', '%s', '%d', '%s', '%s',
				)
			);
		}

		// Also set a transient for fast lookups.
		$cache_duration = absint( get_option( 'azon_mate_cache_duration', 24 ) );
		$transient_key  = $this->get_transient_key( $product->get_asin(), $product->get_marketplace() );
		set_transient( $transient_key, $data, $cache_duration * HOUR_IN_SECONDS );

		return false !== $result;
	}

	/**
	 * Get a cached product.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin        Product ASIN.
	 * @param string $marketplace Marketplace code.
	 * @return Product|null Product instance or null if not found.
	 */
	public function get_product( $asin, $marketplace = 'US' ) {
		// Check transient first (fastest).
		$transient_key = $this->get_transient_key( $asin, $marketplace );
		$cached        = get_transient( $transient_key );

		if ( false !== $cached && is_array( $cached ) ) {
			return Product::from_db_row( (object) $cached );
		}

		// Fall back to database.
		$row = $this->get_product_row( $asin, $marketplace );

		if ( $row ) {
			$product = Product::from_db_row( $row );

			// Re-set transient for next time.
			$cache_duration = absint( get_option( 'azon_mate_cache_duration', 24 ) );
			set_transient( $transient_key, $product->to_db_array(), $cache_duration * HOUR_IN_SECONDS );

			return $product;
		}

		return null;
	}

	/**
	 * Get a raw product row from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin        Product ASIN.
	 * @param string $marketplace Marketplace code.
	 * @return object|null Database row or null.
	 */
	private function get_product_row( $asin, $marketplace ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE asin = %s AND marketplace = %s",
				$asin,
				$marketplace
			)
		);
	}

	/**
	 * Delete a cached product.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin        Product ASIN.
	 * @param string $marketplace Marketplace code.
	 * @return bool
	 */
	public function delete_product( $asin, $marketplace = 'US' ) {
		global $wpdb;

		$transient_key = $this->get_transient_key( $asin, $marketplace );
		delete_transient( $transient_key );

		$result = $wpdb->delete(
			$this->table,
			array(
				'asin'        => $asin,
				'marketplace' => $marketplace,
			),
			array( '%s', '%s' )
		);

		return false !== $result;
	}

	/**
	 * Clear all cached products.
	 *
	 * @since 1.0.0
	 *
	 * @return int Number of rows deleted.
	 */
	public function clear_all() {
		global $wpdb;

		// Delete all transients.
		$wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_azon_mate_product_%' OR option_name LIKE '_transient_timeout_azon_mate_product_%'"
		);

		// Truncate products table.
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table}" );
		$wpdb->query( "TRUNCATE TABLE {$this->table}" );

		/**
		 * Fires after all caches have been cleared.
		 *
		 * @since 1.0.0
		 */
		do_action( 'azon_mate_after_cache_clear' );

		return (int) $count;
	}

	/**
	 * Get stale products that need refreshing.
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit Maximum number of products to return.
	 * @return array Array of objects with asin and marketplace.
	 */
	public function get_stale_products( $limit = 50 ) {
		global $wpdb;

		$cache_duration = absint( get_option( 'azon_mate_cache_duration', 24 ) );
		$stale_date     = gmdate( 'Y-m-d H:i:s', time() - ( $cache_duration * HOUR_IN_SECONDS ) );

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT asin, marketplace FROM {$this->table} WHERE last_updated < %s ORDER BY last_updated ASC LIMIT %d",
				$stale_date,
				$limit
			)
		);
	}

	/**
	 * Get total number of cached products.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_total_cached() {
		global $wpdb;
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table}" );
	}

	/**
	 * Get all manually created products.
	 *
	 * @since 1.1.0
	 *
	 * @return \AzonMate\Models\Product[]
	 */
	public function get_manual_products() {
		global $wpdb;

		$rows = $wpdb->get_results(
			"SELECT * FROM {$this->table} WHERE is_manual = 1 ORDER BY created_at DESC"
		);

		$products = array();
		foreach ( $rows as $row ) {
			$products[] = Product::from_db_row( $row );
		}

		return $products;
	}

	/**
	 * Search manual products by title.
	 *
	 * @since 1.1.0
	 *
	 * @param string $search Search term.
	 * @return \AzonMate\Models\Product[]
	 */
	public function search_manual_products( $search = '' ) {
		global $wpdb;

		if ( empty( $search ) ) {
			return $this->get_manual_products();
		}

		$like = '%' . $wpdb->esc_like( $search ) . '%';
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE is_manual = 1 AND (title LIKE %s OR asin LIKE %s OR brand LIKE %s) ORDER BY created_at DESC",
				$like,
				$like,
				$like
			)
		);

		$products = array();
		foreach ( $rows as $row ) {
			$products[] = Product::from_db_row( $row );
		}

		return $products;
	}

	/**
	 * Delete a manual product by ASIN.
	 *
	 * @since 1.1.0
	 *
	 * @param string $asin Product ASIN/ID.
	 * @return bool
	 */
	public function delete_manual_product( $asin ) {
		global $wpdb;

		$transient_key = $this->get_transient_key( $asin, get_option( 'azon_mate_marketplace', 'US' ) );
		delete_transient( $transient_key );

		$result = $wpdb->delete(
			$this->table,
			array(
				'asin'      => $asin,
				'is_manual' => 1,
			),
			array( '%s', '%d' )
		);

		return false !== $result;
	}

	/**
	 * Get all product ASINs grouped by marketplace.
	 *
	 * @since 1.6.0
	 *
	 * @return array Associative array: marketplace => array of ASINs.
	 */
	public function get_all_product_asins() {
		global $wpdb;

		$rows = $wpdb->get_results(
			"SELECT asin, marketplace FROM {$this->table} ORDER BY marketplace, asin"
		);

		$grouped = array();
		foreach ( $rows as $row ) {
			$market = $row->marketplace ?: 'US';
			if ( ! isset( $grouped[ $market ] ) ) {
				$grouped[ $market ] = array();
			}
			$grouped[ $market ][] = $row->asin;
		}

		return $grouped;
	}

	/**
	 * Build the transient key for a product.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin        Product ASIN.
	 * @param string $marketplace Marketplace code.
	 * @return string
	 */
	private function get_transient_key( $asin, $marketplace ) {
		return 'azon_mate_product_' . $asin . '_' . $marketplace;
	}
}
