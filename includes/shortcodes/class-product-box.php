<?php
/**
 * Product Box shortcode: [azonmate box="ASIN"]
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
 * Class ProductBox
 *
 * @since 1.0.0
 */
class ProductBox extends AbstractShortcode {

	/**
	 * Render the product box.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Not used.
	 * @return string HTML.
	 */
	public function render( array $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'box'         => '',
				'template'    => 'default',
				'title'       => '',
				'description' => 'true',
				'rating'      => 'true',
				'price'       => 'true',
				'button_text' => '',
				'image_size'  => 'medium',
			),
			$atts,
			'azonmate'
		);

		$asin = sanitize_text_field( $atts['box'] );
		if ( empty( $asin ) ) {
			return '';
		}

		$product = $this->get_product( $asin );
		if ( ! $product ) {
			return $this->fallback_output( $asin );
		}

		$data = array(
			'template'    => $atts['template'],
			'product'     => $product,
			'title'       => ! empty( $atts['title'] ) ? $atts['title'] : $product->get_title(),
			'show_desc'   => filter_var( $atts['description'], FILTER_VALIDATE_BOOLEAN ),
			'show_rating' => filter_var( $atts['rating'], FILTER_VALIDATE_BOOLEAN ),
			'show_price'  => filter_var( $atts['price'], FILTER_VALIDATE_BOOLEAN ),
			'button_text' => ! empty( $atts['button_text'] )
				? $atts['button_text']
				: get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ),
			'image_size'  => $atts['image_size'],
		);

		return $this->renderer->render_product_box( $product, $data );
	}
}
