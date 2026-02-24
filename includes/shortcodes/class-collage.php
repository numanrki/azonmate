<?php
/**
 * Product Collage shortcode: [azonmate collage="ASIN1,ASIN2,ASIN3"]
 *
 * Dynamic multi-product collage with hover-reveal buy buttons.
 * Automatically adjusts grid layout based on product count:
 * - 1 product:  Full-width hero
 * - 2 products: 50/50 side-by-side
 * - 3 products: 1 large + 2 small
 * - 4 products: 2×2 grid
 * - 5 products: 1 large + 2×2 grid
 * - 6+ products: Responsive auto-fit grid
 *
 * On hover: reveals buy button and action elements.
 * Non-hover: shows title, price, rating, discount.
 *
 * @package AzonMate\Shortcodes
 * @since   1.6.0
 */

namespace AzonMate\Shortcodes;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Collage
 *
 * @since 1.6.0
 */
class Collage extends AbstractShortcode {

	/**
	 * Render the product collage.
	 *
	 * @since 1.6.0
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Not used.
	 * @return string HTML.
	 */
	public function render( array $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'collage'     => '',
				'max'         => 12,
				'button_text' => '',
				'show_badge'  => 'true',
				'show_price'  => 'true',
				'show_rating' => 'true',
				'heading'     => '',
				'gap'         => '12',
			),
			$atts,
			'azonmate'
		);

		$asins = $this->parse_asins( $atts['collage'] );
		if ( empty( $asins ) ) {
			return '';
		}

		$asins    = array_slice( $asins, 0, absint( $atts['max'] ) );
		$products = $this->get_products( $asins );

		if ( empty( $products ) ) {
			return '';
		}

		$data = array(
			'products'    => $products,
			'count'       => count( $products ),
			'max'         => absint( $atts['max'] ),
			'show_badge'  => filter_var( $atts['show_badge'], FILTER_VALIDATE_BOOLEAN ),
			'show_price'  => filter_var( $atts['show_price'], FILTER_VALIDATE_BOOLEAN ),
			'show_rating' => filter_var( $atts['show_rating'], FILTER_VALIDATE_BOOLEAN ),
			'show_button' => true,
			'heading'     => sanitize_text_field( $atts['heading'] ),
			'gap'         => absint( $atts['gap'] ),
		);

		if ( ! empty( $atts['button_text'] ) ) {
			$data['button_text'] = sanitize_text_field( $atts['button_text'] );
		}

		return $this->renderer->render_collage( $products, $data );
	}
}
