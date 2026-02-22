<?php
/**
 * Image Link shortcode: [azonmate image="ASIN" size="medium"]
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
 * Class ImageLink
 *
 * @since 1.0.0
 */
class ImageLink extends AbstractShortcode {

	/**
	 * Render the image link.
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
				'image' => '',
				'size'  => 'medium',
			),
			$atts,
			'azonmate'
		);

		$asin = sanitize_text_field( $atts['image'] );
		if ( empty( $asin ) ) {
			return '';
		}

		$product = $this->get_product( $asin );
		if ( ! $product ) {
			return $this->fallback_output( $asin );
		}

		$data = array(
			'product'    => $product,
			'image_size' => $atts['size'],
			'asin'       => $asin,
		);

		return $this->renderer->render_image_link( $product, $data );
	}
}
