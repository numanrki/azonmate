<?php
/**
 * Product data model.
 *
 * @package AzonMate\Models
 * @since   1.0.0
 */

namespace AzonMate\Models;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Product
 *
 * Represents a normalized Amazon product with all data points.
 * Provides getters and factory methods for creating product instances
 * from API responses or database rows.
 *
 * @since 1.0.0
 */
class Product {

	/**
	 * Amazon Standard Identification Number.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $asin = '';

	/**
	 * Amazon marketplace code (US, UK, DE, etc.).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $marketplace = 'US';

	/**
	 * Product title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $title = '';

	/**
	 * Full product URL with affiliate tag.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $url = '';

	/**
	 * Small image URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $image_small = '';

	/**
	 * Medium image URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $image_medium = '';

	/**
	 * Large image URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $image_large = '';

	/**
	 * Display-formatted price string (e.g., "$29.99").
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $price_display = '';

	/**
	 * Raw price amount.
	 *
	 * @since 1.0.0
	 * @var float
	 */
	private $price_amount = 0.00;

	/**
	 * Price currency code (e.g., "USD").
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $price_currency = '';

	/**
	 * List price (original price before discount).
	 *
	 * @since 1.0.0
	 * @var float
	 */
	private $list_price_amount = 0.00;

	/**
	 * Savings percentage.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $savings_percentage = 0;

	/**
	 * Star rating (0.0–5.0).
	 *
	 * @since 1.0.0
	 * @var float
	 */
	private $rating = 0.0;

	/**
	 * Total number of reviews.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $review_count = 0;

	/**
	 * Whether the product is Prime eligible.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	private $is_prime = false;

	/**
	 * Availability status string.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $availability = '';

	/**
	 * Brand or manufacturer name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $brand = '';

	/**
	 * Feature bullet points (JSON-encoded array).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $features = array();

	/**
	 * Product description.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $description = '';

	/**
	 * Browse node / category.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $browse_node = '';

	/**
	 * Last time this product data was updated.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $last_updated = '';

	/**
	 * When this product was first cached.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $created_at = '';

	/**
	 * Whether this product was manually created (not from API).
	 *
	 * @since 1.1.0
	 * @var bool
	 */
	private $is_manual = false;

	/**
	 * Badge label for showcase displays (e.g., "Best Value", "Premium Pick").
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private $badge_label = '';

	/**
	 * Custom CTA button text override for this product.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private $button_text = '';

	// -------------------------------------------------------------------------
	// Factory Methods
	// -------------------------------------------------------------------------

	/**
	 * Create a Product instance from an Amazon PA-API 5.0 response item.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $item        A single item from the API response.
	 * @param string $marketplace The marketplace code.
	 * @param string $partner_tag The affiliate partner tag.
	 * @return Product
	 */
	public static function from_api_response( $item, $marketplace = 'US', $partner_tag = '' ) {
		$product = new self();

		$product->asin        = isset( $item['ASIN'] ) ? sanitize_text_field( $item['ASIN'] ) : '';
		$product->marketplace = sanitize_text_field( $marketplace );
		$product->title       = isset( $item['ItemInfo']['Title']['DisplayValue'] ) ? sanitize_text_field( $item['ItemInfo']['Title']['DisplayValue'] ) : '';

		// URL.
		$product->url = isset( $item['DetailPageURL'] ) ? esc_url_raw( $item['DetailPageURL'] ) : '';

		// Images.
		if ( isset( $item['Images']['Primary'] ) ) {
			$images = $item['Images']['Primary'];
			$product->image_small  = isset( $images['Small']['URL'] ) ? esc_url_raw( $images['Small']['URL'] ) : '';
			$product->image_medium = isset( $images['Medium']['URL'] ) ? esc_url_raw( $images['Medium']['URL'] ) : '';
			$product->image_large  = isset( $images['Large']['URL'] ) ? esc_url_raw( $images['Large']['URL'] ) : '';
		}

		// Pricing.
		if ( isset( $item['Offers']['Listings'][0]['Price'] ) ) {
			$price = $item['Offers']['Listings'][0]['Price'];
			$product->price_display  = isset( $price['DisplayAmount'] ) ? sanitize_text_field( $price['DisplayAmount'] ) : '';
			$product->price_amount   = isset( $price['Amount'] ) ? floatval( $price['Amount'] ) : 0.00;
			$product->price_currency = isset( $price['Currency'] ) ? sanitize_text_field( $price['Currency'] ) : '';
		}

		// List price / savings.
		if ( isset( $item['Offers']['Listings'][0]['Price']['Savings'] ) ) {
			$savings = $item['Offers']['Listings'][0]['Price']['Savings'];
			$product->savings_percentage = isset( $savings['Percentage'] ) ? absint( $savings['Percentage'] ) : 0;
		}
		if ( isset( $item['Offers']['Listings'][0]['SavingBasis']['Amount'] ) ) {
			$product->list_price_amount = floatval( $item['Offers']['Listings'][0]['SavingBasis']['Amount'] );
		}

		// Rating.
		if ( isset( $item['CustomerReviews'] ) ) {
			$product->rating       = isset( $item['CustomerReviews']['StarRating']['Value'] ) ? floatval( $item['CustomerReviews']['StarRating']['Value'] ) : 0.0;
			$product->review_count = isset( $item['CustomerReviews']['Count'] ) ? absint( $item['CustomerReviews']['Count'] ) : 0;
		}

		// Prime.
		if ( isset( $item['Offers']['Listings'][0]['DeliveryInfo']['IsPrimeEligible'] ) ) {
			$product->is_prime = (bool) $item['Offers']['Listings'][0]['DeliveryInfo']['IsPrimeEligible'];
		}

		// Availability.
		if ( isset( $item['Offers']['Listings'][0]['Availability']['Message'] ) ) {
			$product->availability = sanitize_text_field( $item['Offers']['Listings'][0]['Availability']['Message'] );
		}

		// Brand.
		if ( isset( $item['ItemInfo']['ByLineInfo']['Brand']['DisplayValue'] ) ) {
			$product->brand = sanitize_text_field( $item['ItemInfo']['ByLineInfo']['Brand']['DisplayValue'] );
		} elseif ( isset( $item['ItemInfo']['ByLineInfo']['Manufacturer']['DisplayValue'] ) ) {
			$product->brand = sanitize_text_field( $item['ItemInfo']['ByLineInfo']['Manufacturer']['DisplayValue'] );
		}

		// Features.
		if ( isset( $item['ItemInfo']['Features']['DisplayValues'] ) && is_array( $item['ItemInfo']['Features']['DisplayValues'] ) ) {
			$product->features = array_map( 'sanitize_text_field', $item['ItemInfo']['Features']['DisplayValues'] );
		}

		// Description.
		if ( isset( $item['ItemInfo']['ProductInfo']['ProductDescription']['DisplayValue'] ) ) {
			$product->description = sanitize_text_field( $item['ItemInfo']['ProductInfo']['ProductDescription']['DisplayValue'] );
		}

		// Browse node.
		if ( isset( $item['BrowseNodeInfo']['BrowseNodes'][0]['DisplayName'] ) ) {
			$product->browse_node = sanitize_text_field( $item['BrowseNodeInfo']['BrowseNodes'][0]['DisplayName'] );
		}

		$product->last_updated = current_time( 'mysql' );
		$product->created_at   = current_time( 'mysql' );

		return $product;
	}

	/**
	 * Create a Product instance from manual user input.
	 *
	 * @since 1.1.0
	 *
	 * @param array $data Associative array of product fields.
	 * @return Product
	 */
	public static function from_manual_input( array $data ) {
		$product = new self();

		$product->asin               = sanitize_text_field( $data['asin'] ?? '' );
		$product->marketplace        = sanitize_text_field( $data['marketplace'] ?? 'US' );
		$product->title              = sanitize_text_field( $data['title'] ?? '' );
		$product->url                = esc_url_raw( $data['url'] ?? '' );
		$product->image_small        = esc_url_raw( $data['image_small'] ?? ( $data['image_url'] ?? '' ) );
		$product->image_medium       = esc_url_raw( $data['image_medium'] ?? ( $data['image_url'] ?? '' ) );
		$product->image_large        = esc_url_raw( $data['image_large'] ?? ( $data['image_url'] ?? '' ) );
		$product->price_display      = sanitize_text_field( $data['price_display'] ?? '' );
		$product->price_amount       = floatval( $data['price_amount'] ?? 0 );
		$product->price_currency     = sanitize_text_field( $data['price_currency'] ?? 'USD' );
		$product->list_price_amount  = floatval( $data['list_price_amount'] ?? 0 );
		$product->savings_percentage = absint( $data['savings_percentage'] ?? 0 );
		$product->rating             = floatval( $data['rating'] ?? 0 );
		$product->review_count       = absint( $data['review_count'] ?? 0 );
		$product->is_prime           = ! empty( $data['is_prime'] );
		$product->availability       = sanitize_text_field( $data['availability'] ?? '' );
		$product->brand              = sanitize_text_field( $data['brand'] ?? '' );
		$product->description        = sanitize_textarea_field( $data['description'] ?? '' );
		$product->browse_node        = sanitize_text_field( $data['browse_node'] ?? '' );
		$product->is_manual          = true;
		$product->badge_label        = sanitize_text_field( $data['badge_label'] ?? '' );
		$product->button_text        = sanitize_text_field( $data['button_text'] ?? '' );

		// Handle features as newline-separated text or array.
		if ( ! empty( $data['features'] ) ) {
			if ( is_array( $data['features'] ) ) {
				$product->features = array_map( 'sanitize_text_field', $data['features'] );
			} else {
				$product->features = array_filter( array_map( 'trim', explode( "\n", $data['features'] ) ) );
			}
		}

		$product->last_updated = current_time( 'mysql' );
		$product->created_at   = current_time( 'mysql' );

		return $product;
	}

	/**
	 * Create a Product instance from a database row.
	 *
	 * @since 1.0.0
	 *
	 * @param object $row Database row object.
	 * @return Product
	 */
	public static function from_db_row( $row ) {
		$product = new self();

		$product->asin               = $row->asin ?? '';
		$product->marketplace        = $row->marketplace ?? 'US';
		$product->title              = $row->title ?? '';
		$product->url                = $row->url ?? '';
		$product->image_small        = $row->image_small ?? '';
		$product->image_medium       = $row->image_medium ?? '';
		$product->image_large        = $row->image_large ?? '';
		$product->price_display      = $row->price_display ?? '';
		$product->price_amount       = floatval( $row->price_amount ?? 0 );
		$product->price_currency     = $row->price_currency ?? '';
		$product->list_price_amount  = floatval( $row->list_price_amount ?? 0 );
		$product->savings_percentage = absint( $row->savings_percentage ?? 0 );
		$product->rating             = floatval( $row->rating ?? 0 );
		$product->review_count       = absint( $row->review_count ?? 0 );
		$product->is_prime           = (bool) ( $row->is_prime ?? false );
		$product->availability       = $row->availability ?? '';
		$product->brand              = $row->brand ?? '';
		$product->description        = $row->description ?? '';
		$product->browse_node        = $row->browse_node ?? '';
		$product->last_updated       = $row->last_updated ?? '';
		$product->created_at         = $row->created_at ?? '';
		$product->is_manual          = ! empty( $row->is_manual );
		$product->badge_label        = $row->badge_label ?? '';
		$product->button_text        = $row->button_text ?? '';

		// Features may be stored as JSON string.
		if ( ! empty( $row->features ) ) {
			$decoded = json_decode( $row->features, true );
			$product->features = is_array( $decoded ) ? $decoded : array();
		}

		return $product;
	}

	// -------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------

	/**
	 * Get the ASIN.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_asin() {
		return $this->asin;
	}

	/**
	 * Get the marketplace.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_marketplace() {
		return $this->marketplace;
	}

	/**
	 * Get the product title.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get the product URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Get an image URL by size.
	 *
	 * @since 1.0.0
	 *
	 * @param string $size Image size: 'small', 'medium', or 'large'.
	 * @return string
	 */
	public function get_image_url( $size = 'medium' ) {
		switch ( $size ) {
			case 'small':
				return $this->image_small;
			case 'large':
				return $this->image_large;
			case 'medium':
			default:
				return $this->image_medium;
		}
	}

	/**
	 * Get the display price string.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_price_display() {
		return $this->price_display;
	}

	/**
	 * Get the raw price amount.
	 *
	 * @since 1.0.0
	 * @return float
	 */
	public function get_price_amount() {
		return $this->price_amount;
	}

	/**
	 * Get the price currency code.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_price_currency() {
		return $this->price_currency;
	}

	/**
	 * Get the list price amount.
	 *
	 * @since 1.0.0
	 * @return float
	 */
	public function get_list_price_amount() {
		return $this->list_price_amount;
	}

	/**
	 * Get the savings percentage.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_savings_percentage() {
		return $this->savings_percentage;
	}

	/**
	 * Get the star rating.
	 *
	 * @since 1.0.0
	 * @return float
	 */
	public function get_rating() {
		return $this->rating;
	}

	/**
	 * Get the review count.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_review_count() {
		return $this->review_count;
	}

	/**
	 * Check if the product is Prime eligible.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_prime() {
		return $this->is_prime;
	}

	/**
	 * Get the availability status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_availability() {
		return $this->availability;
	}

	/**
	 * Get the brand name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_brand() {
		return $this->brand;
	}

	/**
	 * Get the feature bullet points.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_features() {
		return $this->features;
	}

	/**
	 * Get the product description.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get the browse node / category.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_browse_node() {
		return $this->browse_node;
	}

	/**
	 * Check if this is a manually created product.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_manual() {
		return $this->is_manual;
	}

	/**
	 * Get the badge label (e.g., "Best Value", "Premium Pick").
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function get_badge_label() {
		return $this->badge_label;
	}

	/**
	 * Get the custom CTA button text for this product.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function get_button_text() {
		return $this->button_text;
	}

	// -------------------------------------------------------------------------
	// Template Alias Methods
	// -------------------------------------------------------------------------

	/**
	 * Alias: Get the product detail page URL.
	 *
	 * Templates often reference this as get_detail_page_url().
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_detail_page_url() {
		return $this->url;
	}

	/**
	 * Alias: Get the formatted display price.
	 *
	 * Templates reference this as get_price().
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_price() {
		return $this->price_display;
	}

	/**
	 * Alias: Get the formatted list (original) price.
	 *
	 * Templates reference this as get_list_price().
	 * Returns a formatted string using the same currency as price_display.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_list_price() {
		if ( $this->list_price_amount <= 0 ) {
			return '';
		}

		// Try to format using the same currency symbol as the display price.
		if ( ! empty( $this->price_display ) && preg_match( '/^([^\d\s.,]+)/', $this->price_display, $m ) ) {
			return $m[1] . number_format( $this->list_price_amount, 2 );
		}

		// Fallback: use currency code or dollar sign.
		$symbol = ! empty( $this->price_currency ) ? $this->price_currency . ' ' : '$';
		return $symbol . number_format( $this->list_price_amount, 2 );
	}

	/**
	 * Get the last updated timestamp.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_last_updated() {
		return $this->last_updated;
	}

	/**
	 * Check if the cached data is stale (older than cache duration).
	 *
	 * @since 1.0.0
	 *
	 * @param int $max_age_hours Maximum age in hours.
	 * @return bool
	 */
	public function is_stale( $max_age_hours = 24 ) {
		if ( empty( $this->last_updated ) ) {
			return true;
		}
		$last_updated_time = strtotime( $this->last_updated );
		$max_age_seconds   = $max_age_hours * HOUR_IN_SECONDS;
		return ( time() - $last_updated_time ) > $max_age_seconds;
	}

	/**
	 * Check if product has valid data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_valid() {
		return ! empty( $this->asin ) && ! empty( $this->title );
	}

	/**
	 * Convert product data to an associative array.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function to_array() {
		return array(
			'asin'               => $this->asin,
			'marketplace'        => $this->marketplace,
			'title'              => $this->title,
			'url'                => $this->url,
			'image_small'        => $this->image_small,
			'image_medium'       => $this->image_medium,
			'image_large'        => $this->image_large,
			'price_display'      => $this->price_display,
			'price_amount'       => $this->price_amount,
			'price_currency'     => $this->price_currency,
			'list_price_amount'  => $this->list_price_amount,
			'savings_percentage' => $this->savings_percentage,
			'rating'             => $this->rating,
			'review_count'       => $this->review_count,
			'is_prime'           => $this->is_prime,
			'availability'       => $this->availability,
			'brand'              => $this->brand,
			'features'           => $this->features,
			'description'        => $this->description,
			'browse_node'        => $this->browse_node,
			'is_manual'          => $this->is_manual,
			'badge_label'        => $this->badge_label,
			'button_text'        => $this->button_text,
			'last_updated'       => $this->last_updated,
			'created_at'         => $this->created_at,
		);
	}

	/**
	 * Convert product data to a database-ready array (features as JSON).
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function to_db_array() {
		$data             = $this->to_array();
		$data['features'] = wp_json_encode( $this->features );
		$data['is_prime'] = $this->is_prime ? 1 : 0;
		$data['is_manual'] = $this->is_manual ? 1 : 0;
		return $data;
	}
}
