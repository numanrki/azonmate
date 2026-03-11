<?php
/**
 * Amazon Creators API client.
 *
 * @package AzonMate\API
 * @since   1.0.0
 */

namespace AzonMate\API;

use AzonMate\Models\Product;
use AzonMate\Cache\CacheManager;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AmazonAPI
 *
 * Provides methods to interact with the Amazon Creators API
 * including searchItems, getItems, and getBrowseNodes operations.
 *
 * @since 1.0.0
 */
class AmazonAPI {

	/**
	 * Cache manager instance.
	 *
	 * @since 1.0.0
	 * @var CacheManager
	 */
	private $cache;

	/**
	 * OAuth client instance (built per-request based on marketplace).
	 *
	 * @since 1.0.0
	 * @var RequestSigner|null
	 */
	private $signer = null;

	/**
	 * Last request timestamp (for rate limiting).
	 *
	 * @since 1.0.0
	 * @var float
	 */
	private static $last_request_time = 0;

	/**
	 * The resources to request from the Creators API.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $default_resources = array(
		'images.primary.small',
		'images.primary.medium',
		'images.primary.large',
		'itemInfo.title',
		'itemInfo.byLineInfo',
		'itemInfo.features',
		'itemInfo.productInfo',
		'offersV2.listings.price',
		'offersV2.listings.availability',
		'offersV2.listings.condition',
		'offersV2.listings.merchantInfo',
		'offersV2.listings.isBuyBoxWinner',
		'browseNodeInfo.browseNodes',
	);

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param CacheManager $cache Cache manager instance.
	 */
	public function __construct( CacheManager $cache ) {
		$this->cache = $cache;
	}

	/**
	 * Get decrypted API credentials.
	 *
	 * @since 1.0.0
	 *
	 * @return array {
	 *     @type string $credential_id     Decrypted credential ID.
	 *     @type string $credential_secret Decrypted credential secret.
	 *     @type string $version           Credential version.
	 *     @type string $partner_tag       Partner/affiliate tag.
	 *     @type string $marketplace       Default marketplace code.
	 * }
	 */
	private function get_credentials() {
		$credential_id     = $this->decrypt_key( get_option( 'azon_mate_credential_id', '' ) );
		$credential_secret = $this->decrypt_key( get_option( 'azon_mate_credential_secret', '' ) );

		return array(
			'credential_id'     => $credential_id,
			'credential_secret' => $credential_secret,
			'version'           => get_option( 'azon_mate_credential_version', '2.1' ),
			'partner_tag'       => get_option( 'azon_mate_partner_tag', '' ),
			'marketplace'       => get_option( 'azon_mate_marketplace', 'US' ),
		);
	}

	/**
	 * Decrypt an API key from storage.
	 *
	 * @since 1.0.0
	 *
	 * @param string $encrypted_key The encrypted key.
	 * @return string Decrypted key, or empty string on failure.
	 */
	private function decrypt_key( $encrypted_key ) {
		if ( empty( $encrypted_key ) ) {
			return '';
		}

		// If the key doesn't appear to be encrypted (e.g., plain text stored during setup), return as-is.
		if ( ! defined( 'AUTH_KEY' ) || ! defined( 'AUTH_SALT' ) || empty( AUTH_KEY ) || empty( AUTH_SALT ) ) {
			return $encrypted_key;
		}

		// Ensure AUTH_SALT is correct length for IV (16 bytes for AES-256-CBC).
		$iv = substr( AUTH_SALT, 0, 16 );
		if ( strlen( $iv ) < 16 ) {
			$iv = str_pad( $iv, 16, "\0" );
		}

		$decrypted = openssl_decrypt( $encrypted_key, 'AES-256-CBC', AUTH_KEY, 0, $iv );

		return false !== $decrypted ? $decrypted : $encrypted_key;
	}

	/**
	 * Encrypt an API key for storage.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plain_key The plain text key.
	 * @return string Encrypted key.
	 */
	public static function encrypt_key( $plain_key ) {
		if ( empty( $plain_key ) ) {
			return '';
		}

		if ( ! defined( 'AUTH_KEY' ) || ! defined( 'AUTH_SALT' ) || empty( AUTH_KEY ) || empty( AUTH_SALT ) ) {
			return $plain_key;
		}

		$iv = substr( AUTH_SALT, 0, 16 );
		if ( strlen( $iv ) < 16 ) {
			$iv = str_pad( $iv, 16, "\0" );
		}

		$encrypted = openssl_encrypt( $plain_key, 'AES-256-CBC', AUTH_KEY, 0, $iv );

		return false !== $encrypted ? $encrypted : $plain_key;
	}

	/**
	 * Build an OAuth client for the given marketplace.
	 *
	 * @since 1.0.0
	 *
	 * @param string $marketplace Marketplace code.
	 * @return RequestSigner|\WP_Error
	 */
	private function get_signer( $marketplace = '' ) {
		$creds = $this->get_credentials();

		if ( empty( $creds['credential_id'] ) || empty( $creds['credential_secret'] ) ) {
			return new \WP_Error( 'azon_mate_no_credentials', __( 'Amazon API credentials are not configured.', 'azonmate' ) );
		}

		if ( empty( $marketplace ) ) {
			$marketplace = $creds['marketplace'];
		}

		$domain = 'www.' . Marketplace::get_domain( $marketplace );

		return new RequestSigner( $creds['credential_id'], $creds['credential_secret'], $creds['version'], $domain );
	}

	/**
	 * Throttle API requests to respect rate limits.
	 *
	 * @since 1.0.0
	 */
	private function throttle() {
		$throttle = absint( get_option( 'azon_mate_api_throttle', 1 ) );
		if ( $throttle < 1 ) {
			$throttle = 1;
		}

		$interval = 1.0 / $throttle;
		$now      = microtime( true );
		$diff     = $now - self::$last_request_time;

		if ( $diff < $interval ) {
			usleep( (int) ( ( $interval - $diff ) * 1000000 ) );
		}

		self::$last_request_time = microtime( true );
	}

	/**
	 * Search for products by keyword.
	 *
	 * @since 1.0.0
	 *
	 * @param string $keywords    Search keywords.
	 * @param string $marketplace Marketplace code.
	 * @param int    $page        Page number (1-10).
	 * @param string $category    Search index (category filter).
	 * @param string $sort_by     Sort option.
	 * @return array|WP_Error {
	 *     @type Product[] $products Array of product objects.
	 *     @type int       $total    Total results.
	 *     @type int       $pages    Total pages.
	 * }
	 */
	public function search_items( $keywords, $marketplace = '', $page = 1, $category = 'All', $sort_by = 'Relevance' ) {
		$creds = $this->get_credentials();
		if ( empty( $marketplace ) ) {
			$marketplace = $creds['marketplace'];
		}

		$signer = $this->get_signer( $marketplace );
		if ( is_wp_error( $signer ) ) {
			return $signer;
		}

		// Build payload.
		$payload = array(
			'keywords'    => $keywords,
			'searchIndex' => $category,
			'itemCount'   => 10,
			'itemPage'    => min( max( 1, $page ), 10 ),
			'partnerTag'  => $creds['partner_tag'],
			'marketplace' => 'www.' . Marketplace::get_domain( $marketplace ),
			'resources'   => $this->default_resources,
		);

		if ( 'Relevance' !== $sort_by ) {
			$payload['sortBy'] = $sort_by;
		}

		/**
		 * Filter the SearchItems API request payload.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $payload     Request payload.
		 * @param string $keywords    Search keywords.
		 * @param string $marketplace Marketplace code.
		 */
		$payload = apply_filters( 'azon_mate_search_items_payload', $payload, $keywords, $marketplace );

		$this->throttle();

		$response = $signer->send_request( 'searchItems', $payload );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Parse results.
		$products = array();
		if ( isset( $response['searchResult']['items'] ) ) {
			foreach ( $response['searchResult']['items'] as $item ) {
				$product = Product::from_api_response( $item, $marketplace, $creds['partner_tag'] );
				if ( $product->is_valid() ) {
					$products[] = $product;

					// Cache each product.
					$this->cache->save_product( $product );
				}
			}
		}

		$total = isset( $response['searchResult']['totalResultCount'] ) ? absint( $response['searchResult']['totalResultCount'] ) : count( $products );
		$pages = min( 10, (int) ceil( $total / 10 ) );

		return array(
			'products' => $products,
			'total'    => $total,
			'pages'    => $pages,
		);
	}

	/**
	 * Get products by ASIN(s).
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $asins       Single ASIN or array of ASINs (max 10).
	 * @param string       $marketplace Marketplace code.
	 * @param bool         $force_fresh Force fresh API call (bypass cache).
	 * @return Product[]|WP_Error Array of Product objects or WP_Error.
	 */
	public function get_items( $asins, $marketplace = '', $force_fresh = false ) {
		$creds = $this->get_credentials();
		if ( empty( $marketplace ) ) {
			$marketplace = $creds['marketplace'];
		}

		if ( is_string( $asins ) ) {
			$asins = array_map( 'trim', explode( ',', $asins ) );
		}

		// Sanitize ASINs.
		$asins = array_filter( array_map( 'sanitize_text_field', $asins ) );
		$asins = array_slice( $asins, 0, 10 ); // Max 10 per API call.

		if ( empty( $asins ) ) {
			return new \WP_Error( 'azon_mate_no_asins', __( 'No ASINs provided.', 'azonmate' ) );
		}

		// Check cache first (unless force_fresh).
		$products     = array();
		$uncached     = array();
		$cache_duration = absint( get_option( 'azon_mate_cache_duration', 24 ) );

		if ( ! $force_fresh && '1' === get_option( 'azon_mate_cache_enabled', '1' ) ) {
			foreach ( $asins as $asin ) {
				$cached = $this->cache->get_product( $asin, $marketplace );
				if ( $cached && ! $cached->is_stale( $cache_duration ) ) {
					$products[ $asin ] = $cached;
				} else {
					$uncached[] = $asin;
				}
			}
		} else {
			$uncached = $asins;
		}

		// Fetch uncached products from API.
		if ( ! empty( $uncached ) ) {
			$signer = $this->get_signer( $marketplace );
			if ( is_wp_error( $signer ) ) {
				// Return cached products if available, even if some are missing.
				if ( ! empty( $products ) ) {
					return array_values( $products );
				}
				return $signer;
			}

			$payload = array(
				'itemIds'     => array_values( $uncached ),
				'itemIdType'  => 'ASIN',
				'partnerTag'  => $creds['partner_tag'],
				'marketplace' => 'www.' . Marketplace::get_domain( $marketplace ),
				'resources'   => $this->default_resources,
			);

			/**
			 * Filter the GetItems API request payload.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $payload     Request payload.
			 * @param array  $asins       ASINs being fetched.
			 * @param string $marketplace Marketplace code.
			 */
			$payload = apply_filters( 'azon_mate_get_items_payload', $payload, $uncached, $marketplace );

			$this->throttle();

			$response = $signer->send_request( 'getItems', $payload );

			if ( ! is_wp_error( $response ) && isset( $response['itemsResult']['items'] ) ) {
				foreach ( $response['itemsResult']['items'] as $item ) {
					$product = Product::from_api_response( $item, $marketplace, $creds['partner_tag'] );
					if ( $product->is_valid() ) {
						$products[ $product->get_asin() ] = $product;
						$this->cache->save_product( $product );
					}
				}
			} elseif ( is_wp_error( $response ) && empty( $products ) ) {
				return $response;
			}
		}

		// Return products in the original ASIN order.
		$ordered = array();
		foreach ( $asins as $asin ) {
			if ( isset( $products[ $asin ] ) ) {
				$ordered[] = $products[ $asin ];
			}
		}

		return $ordered;
	}

	/**
	 * Get a single product by ASIN.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin        Product ASIN.
	 * @param string $marketplace Marketplace code.
	 * @param bool   $force_fresh Bypass cache.
	 * @return Product|WP_Error
	 */
	public function get_item( $asin, $marketplace = '', $force_fresh = false ) {
		$result = $this->get_items( $asin, $marketplace, $force_fresh );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( empty( $result ) ) {
			return new \WP_Error( 'azon_mate_product_not_found', __( 'Product not found in Amazon catalog.', 'azonmate' ) );
		}

		return $result[0];
	}

	/**
	 * Get browse node information.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $browse_node_ids Array of browse node IDs.
	 * @param string $marketplace     Marketplace code.
	 * @return array|WP_Error
	 */
	public function get_browse_nodes( $browse_node_ids, $marketplace = '' ) {
		$creds = $this->get_credentials();
		if ( empty( $marketplace ) ) {
			$marketplace = $creds['marketplace'];
		}

		$signer = $this->get_signer( $marketplace );
		if ( is_wp_error( $signer ) ) {
			return $signer;
		}

		$payload = array(
			'browseNodeIds' => $browse_node_ids,
			'partnerTag'    => $creds['partner_tag'],
			'marketplace'   => 'www.' . Marketplace::get_domain( $marketplace ),
			'resources'     => array(
				'browseNodes.ancestor',
				'browseNodes.children',
			),
		);

		$this->throttle();

		return $signer->send_request( 'getBrowseNodes', $payload );
	}

	/**
	 * Test API connection with a sample search.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function test_connection() {
		$result = $this->search_items( 'test', '', 1, 'All' );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}
}
