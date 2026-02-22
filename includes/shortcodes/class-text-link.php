<?php
/**
 * Text Link shortcode: [azonmate link="ASIN"]anchor[/azonmate]
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
 * Class TextLink
 *
 * @since 1.0.0
 */
class TextLink extends AbstractShortcode {

	/**
	 * Render the text link.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed anchor text.
	 * @return string HTML.
	 */
	public function render( array $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'link'  => '',
				'title' => '',
			),
			$atts,
			'azonmate'
		);

		$asin = sanitize_text_field( $atts['link'] );
		if ( empty( $asin ) ) {
			return '';
		}

		$product = $this->get_product( $asin );

		// Determine anchor text.
		if ( ! empty( $content ) ) {
			$anchor = wp_kses_post( $content );
		} elseif ( ! empty( $atts['title'] ) ) {
			$anchor = esc_html( $atts['title'] );
		} elseif ( $product ) {
			$anchor = esc_html( $product->get_title() );
		} else {
			$anchor = sprintf(
				/* translators: %s: ASIN */
				esc_html__( 'View on Amazon (%s)', 'azonmate' ),
				esc_html( $asin )
			);
		}

		$data = array(
			'product' => $product,
			'anchor'  => $anchor,
			'asin'    => $asin,
		);

		return $this->renderer->render_text_link( $product, $anchor, $data );
	}
}
