<?php
/**
 * Amazon Creators API client — backed by the official PHP SDK.
 *
 * @package AzonMate\API
 * @since   1.0.0
 */

namespace AzonMate\API;

use AzonMate\Models\Product;
use AzonMate\Cache\CacheManager;
use Amazon\CreatorsAPI\v1\Configuration;
use Amazon\CreatorsAPI\v1\ApiException;
use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResource;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesRequestContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResource;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AmazonAPI
 *
 * Provides methods to interact with the Amazon Creators API
 * using the official Amazon PHP SDK for authentication and requests.
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
	 * Last request timestamp (for rate limiting).
	 *
	 * @since 1.0.0
	 * @var float
	 */
	private static $last_request_time = 0;

	/**
	 * Default search resources requested from the Creators API.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	private $search_resources = array(
		SearchItemsResource::IMAGES_PRIMARY_SMALL,
		SearchItemsResource::IMAGES_PRIMARY_MEDIUM,
		SearchItemsResource::IMAGES_PRIMARY_LARGE,
		SearchItemsResource::ITEM_INFO_TITLE,
		SearchItemsResource::ITEM_INFO_BY_LINE_INFO,
		SearchItemsResource::ITEM_INFO_FEATURES,
		SearchItemsResource::ITEM_INFO_PRODUCT_INFO,
		SearchItemsResource::OFFERS_V2_LISTINGS_PRICE,
		SearchItemsResource::OFFERS_V2_LISTINGS_AVAILABILITY,
		SearchItemsResource::OFFERS_V2_LISTINGS_CONDITION,
		SearchItemsResource::OFFERS_V2_LISTINGS_MERCHANT_INFO,
		SearchItemsResource::OFFERS_V2_LISTINGS_IS_BUY_BOX_WINNER,
		SearchItemsResource::BROWSE_NODE_INFO_BROWSE_NODES,
	);

	/**
	 * Default getItems resources requested from the Creators API.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	private $get_items_resources = array(
		GetItemsResource::IMAGES_PRIMARY_SMALL,
		GetItemsResource::IMAGES_PRIMARY_MEDIUM,
		GetItemsResource::IMAGES_PRIMARY_LARGE,
		GetItemsResource::ITEM_INFO_TITLE,
		GetItemsResource::ITEM_INFO_BY_LINE_INFO,
		GetItemsResource::ITEM_INFO_FEATURES,
		GetItemsResource::ITEM_INFO_PRODUCT_INFO,
		GetItemsResource::OFFERS_V2_LISTINGS_PRICE,
		GetItemsResource::OFFERS_V2_LISTINGS_AVAILABILITY,
		GetItemsResource::OFFERS_V2_LISTINGS_CONDITION,
		GetItemsResource::OFFERS_V2_LISTINGS_MERCHANT_INFO,
		GetItemsResource::OFFERS_V2_LISTINGS_IS_BUY_BOX_WINNER,
		GetItemsResource::BROWSE_NODE_INFO_BROWSE_NODES,
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

		if ( ! defined( 'AUTH_KEY' ) || ! defined( 'AUTH_SALT' ) || empty( AUTH_KEY ) || empty( AUTH_SALT ) ) {
			return $encrypted_key;
		}

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
	 * Build an SDK API client for the given marketplace.
	 *
	 * @since 2.1.0
	 *
	 * @param string $marketplace Marketplace code (optional — uses default from settings).
	 * @return array{api: DefaultApi, marketplace_domain: string, partner_tag: string}|\WP_Error
	 */
	private function get_sdk_client( $marketplace = '' ) {
		$creds = $this->get_credentials();

		if ( empty( $creds['credential_id'] ) || empty( $creds['credential_secret'] ) ) {
			return new \WP_Error( 'azon_mate_no_credentials', __( 'Amazon API credentials are not configured.', 'azonmate' ) );
		}

		if ( empty( $marketplace ) ) {
			$marketplace = $creds['marketplace'];
		}

		$config = new Configuration();
		$config->setCredentialId( $creds['credential_id'] );
		$config->setCredentialSecret( $creds['credential_secret'] );
		$config->setVersion( $creds['version'] );

		if ( \AzonMate\Plugin::is_debug_enabled() ) {
			$config->setDebug( true );
		}

		$api = new DefaultApi( null, $config );

		return array(
			'api'                => $api,
			'marketplace_domain' => 'www.' . Marketplace::get_domain( $marketplace ),
			'partner_tag'        => $creds['partner_tag'],
			'marketplace_code'   => $marketplace,
		);
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
	 * @return array|\WP_Error {
	 *     @type Product[] $products Array of product objects.
	 *     @type int       $total    Total results.
	 *     @type int       $pages    Total pages.
	 * }
	 */
	public function search_items( $keywords, $marketplace = '', $page = 1, $category = 'All', $sort_by = 'Relevance' ) {
		$client = $this->get_sdk_client( $marketplace );
		if ( is_wp_error( $client ) ) {
			return $client;
		}

		$request = new SearchItemsRequestContent();
		$request->setKeywords( $keywords );
		$request->setSearchIndex( $category );
		$request->setItemCount( 10 );
		$request->setItemPage( min( max( 1, $page ), 10 ) );
		$request->setPartnerTag( $client['partner_tag'] );
		$request->setResources( $this->search_resources );

		if ( 'Relevance' !== $sort_by ) {
			$request->setSortBy( $sort_by );
		}

		$this->throttle();

		try {
			$response = $client['api']->searchItems( $client['marketplace_domain'], $request );
		} catch ( ApiException $e ) {
			return new \WP_Error(
				'azon_mate_api_error',
				sprintf( 'Amazon API error %d: %s', $e->getCode(), $e->getMessage() )
			);
		} catch ( \Exception $e ) {
			return new \WP_Error( 'azon_mate_api_error', $e->getMessage() );
		}

		// Parse results from SDK response objects.
		$products      = array();
		$search_result = $response->getSearchResult();

		if ( $search_result && $search_result->getItems() ) {
			foreach ( $search_result->getItems() as $item ) {
				$product = Product::from_api_response( $item, $client['marketplace_code'], $client['partner_tag'] );
				if ( $product->is_valid() ) {
					$products[] = $product;
					$this->cache->save_product( $product );
				}
			}
		}

		$total = $search_result ? (int) $search_result->getTotalResultCount() : count( $products );
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
	 * @return Product[]|\WP_Error Array of Product objects or WP_Error.
	 */
	public function get_items( $asins, $marketplace = '', $force_fresh = false ) {
		$creds = $this->get_credentials();
		if ( empty( $marketplace ) ) {
			$marketplace = $creds['marketplace'];
		}

		if ( is_string( $asins ) ) {
			$asins = array_map( 'trim', explode( ',', $asins ) );
		}

		$asins = array_filter( array_map( 'sanitize_text_field', $asins ) );
		$asins = array_slice( $asins, 0, 10 );

		if ( empty( $asins ) ) {
			return new \WP_Error( 'azon_mate_no_asins', __( 'No ASINs provided.', 'azonmate' ) );
		}

		// Check cache first (unless force_fresh).
		$products       = array();
		$uncached       = array();
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
			$client = $this->get_sdk_client( $marketplace );
			if ( is_wp_error( $client ) ) {
				if ( ! empty( $products ) ) {
					return array_values( $products );
				}
				return $client;
			}

			$request = new GetItemsRequestContent();
			$request->setItemIds( array_values( $uncached ) );
			$request->setPartnerTag( $client['partner_tag'] );
			$request->setResources( $this->get_items_resources );

			$this->throttle();

			try {
				$response     = $client['api']->getItems( $client['marketplace_domain'], $request );
				$items_result = $response->getItemsResult();

				if ( $items_result && $items_result->getItems() ) {
					foreach ( $items_result->getItems() as $item ) {
						$product = Product::from_api_response( $item, $client['marketplace_code'], $client['partner_tag'] );
						if ( $product->is_valid() ) {
							$products[ $product->get_asin() ] = $product;
							$this->cache->save_product( $product );
						}
					}
				}
			} catch ( ApiException $e ) {
				if ( empty( $products ) ) {
					return new \WP_Error(
						'azon_mate_api_error',
						sprintf( 'Amazon API error %d: %s', $e->getCode(), $e->getMessage() )
					);
				}
			} catch ( \Exception $e ) {
				if ( empty( $products ) ) {
					return new \WP_Error( 'azon_mate_api_error', $e->getMessage() );
				}
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
	 * @return Product|\WP_Error
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
	 * @return array|\WP_Error
	 */
	public function get_browse_nodes( $browse_node_ids, $marketplace = '' ) {
		$client = $this->get_sdk_client( $marketplace );
		if ( is_wp_error( $client ) ) {
			return $client;
		}

		$request = new GetBrowseNodesRequestContent();
		$request->setBrowseNodeIds( $browse_node_ids );
		$request->setPartnerTag( $client['partner_tag'] );
		$request->setResources( array(
			GetBrowseNodesResource::ANCESTOR,
			GetBrowseNodesResource::CHILDREN,
		) );

		$this->throttle();

		try {
			$response = $client['api']->getBrowseNodes( $client['marketplace_domain'], $request );
			return json_decode( wp_json_encode( $response ), true );
		} catch ( ApiException $e ) {
			return new \WP_Error(
				'azon_mate_api_error',
				sprintf( 'Amazon API error %d: %s', $e->getCode(), $e->getMessage() )
			);
		} catch ( \Exception $e ) {
			return new \WP_Error( 'azon_mate_api_error', $e->getMessage() );
		}
	}

	/**
	 * Test API connection with a sample search.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error True on success, WP_Error on failure.
	 */
	public function test_connection() {
		$result = $this->search_items( 'test', '', 1, 'All' );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}
}
