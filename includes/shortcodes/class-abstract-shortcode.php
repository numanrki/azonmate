<?php
/**
 * Base shortcode handler.
 *
 * @package AzonMate\Shortcodes
 * @since   1.0.0
 */

namespace AzonMate\Shortcodes;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AbstractShortcode
 *
 * Shared functionality for all shortcode handlers.
 *
 * @since 1.0.0
 */
abstract class AbstractShortcode {

	/**
	 * @var \AzonMate\API\AmazonAPI
	 */
	protected $api;

	/**
	 * @var \AzonMate\Templates\TemplateRenderer
	 */
	protected $renderer;

	/**
	 * @var \AzonMate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param \AzonMate\API\AmazonAPI              $api      API client.
	 * @param \AzonMate\Templates\TemplateRenderer  $renderer Renderer.
	 * @param \AzonMate\Cache\CacheManager          $cache    Cache.
	 */
	public function __construct( $api, $renderer, $cache ) {
		$this->api      = $api;
		$this->renderer = $renderer;
		$this->cache    = $cache;
	}

	/**
	 * Render the shortcode output.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed content.
	 * @return string HTML.
	 */
	abstract public function render( array $atts, $content = null );

	/**
	 * Fetch a single product by ASIN — cache-first.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin Product ASIN.
	 * @return \AzonMate\Models\Product|null
	 */
	protected function get_product( $asin ) {
		$product = $this->cache->get_product( $asin );

		if ( $product ) {
			return $product;
		}

		$products = $this->api->get_items( array( $asin ) );
		return $products[0] ?? null;
	}

	/**
	 * Fetch multiple products by ASINs — cache-first.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $asins Array of ASINs.
	 * @return \AzonMate\Models\Product[]
	 */
	protected function get_products( array $asins ) {
		$products  = array();
		$to_fetch  = array();

		foreach ( $asins as $asin ) {
			$cached = $this->cache->get_product( $asin );
			if ( $cached ) {
				$products[ $asin ] = $cached;
			} else {
				$to_fetch[] = $asin;
			}
		}

		if ( ! empty( $to_fetch ) ) {
			// PA-API allows max 10 ASINs per GetItems request.
			$chunks = array_chunk( $to_fetch, 10 );
			foreach ( $chunks as $chunk ) {
				$fetched = $this->api->get_items( $chunk );
				foreach ( $fetched as $product ) {
					$products[ $product->get_asin() ] = $product;
				}
			}
		}

		// Maintain original ASIN order.
		$ordered = array();
		foreach ( $asins as $asin ) {
			if ( isset( $products[ $asin ] ) ) {
				$ordered[] = $products[ $asin ];
			}
		}

		return $ordered;
	}

	/**
	 * Parse a comma-separated string of ASINs.
	 *
	 * @since 1.0.0
	 *
	 * @param string $csv ASIN list.
	 * @return string[]
	 */
	protected function parse_asins( $csv ) {
		return array_values(
			array_filter(
				array_map( 'trim', explode( ',', $csv ) )
			)
		);
	}

	/**
	 * Return a fallback message when the product can't be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin ASIN that failed.
	 * @return string
	 */
	protected function fallback_output( $asin ) {
		$title = sprintf(
			/* translators: %s: ASIN */
			esc_html__( 'Check product on Amazon (%s)', 'azonmate' ),
			esc_html( $asin )
		);

		$marketplace = get_option( 'azon_mate_marketplace', 'www' );
		$tag         = get_option( 'azon_mate_partner_tag', '' );
		$url         = sprintf(
			'https://%s/dp/%s?tag=%s',
			esc_attr( $marketplace ),
			esc_attr( $asin ),
			esc_attr( $tag )
		);

		return sprintf(
			'<a href="%s" class="azonmate-fallback-link" target="_blank" rel="nofollow noopener sponsored">%s</a>',
			esc_url( $url ),
			$title
		);
	}
}
