<?php
/**
 * Product Showcase shortcode: [azonmate showcase="ASIN1,ASIN2,ASIN3" layout="grid"]
 *
 * Premium product display with multiple layout options:
 * Multi-product:
 * - grid:    Responsive card grid (default)
 * - list:    Horizontal row / list layout
 * - masonry: Pinterest-style masonry / collage
 * - table:   Modern comparison table
 * Single-product:
 * - hero:    Featured large hero card
 * - compact: Slim inline card for mid-article
 * - split:   50/50 image + details panel
 * - deal:    Price-drop focused card
 *
 * @package AzonMate\Shortcodes
 * @since   1.2.0
 */

namespace AzonMate\Shortcodes;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Showcase
 *
 * @since 1.2.0
 */
class Showcase extends AbstractShortcode {

	/**
	 * Render the product showcase.
	 *
	 * @since 1.2.0
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Not used.
	 * @return string HTML.
	 */
	public function render( array $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'showcase'    => '',
				'layout'      => 'grid',
				'columns'     => '',
				'max'         => 12,
				'button_text' => '',
				'show_badge'  => 'true',
				'show_price'  => 'true',
				'show_rating' => 'true',
				'heading'     => '',
			),
			$atts,
			'azonmate'
		);

		$asins = $this->parse_asins( $atts['showcase'] );
		if ( empty( $asins ) ) {
			return '';
		}

		$asins    = array_slice( $asins, 0, absint( $atts['max'] ) );
		$products = $this->get_products( $asins );

		if ( empty( $products ) ) {
			return '';
		}

		$data = array(
			'layout'      => sanitize_key( $atts['layout'] ),
			'columns'     => ! empty( $atts['columns'] ) ? absint( $atts['columns'] ) : 0,
			'max'         => absint( $atts['max'] ),
			'show_badge'  => filter_var( $atts['show_badge'], FILTER_VALIDATE_BOOLEAN ),
			'show_price'  => filter_var( $atts['show_price'], FILTER_VALIDATE_BOOLEAN ),
			'show_rating' => filter_var( $atts['show_rating'], FILTER_VALIDATE_BOOLEAN ),
			'show_button' => true,
			'heading'     => sanitize_text_field( $atts['heading'] ),
		);

		if ( ! empty( $atts['button_text'] ) ) {
			$data['button_text'] = sanitize_text_field( $atts['button_text'] );
		}

		return $this->renderer->render_showcase( $products, $data );
	}
}
