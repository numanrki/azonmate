<?php
/**
 * Product List shortcode: [azonmate list="ASIN1,ASIN2,ASIN3"]
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
 * Class ProductList
 *
 * @since 1.0.0
 */
class ProductList extends AbstractShortcode {

	/**
	 * Render the product list.
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
				'list'     => '',
				'template' => 'default',
				'max'      => 10,
			),
			$atts,
			'azonmate'
		);

		$asins = $this->parse_asins( $atts['list'] );
		if ( empty( $asins ) ) {
			return '';
		}

		$asins    = array_slice( $asins, 0, absint( $atts['max'] ) );
		$products = $this->get_products( $asins );

		if ( empty( $products ) ) {
			return '';
		}

		$data = array(
			'products' => $products,
			'template' => $atts['template'],
		);

		return $this->renderer->render_product_list( $products, $data );
	}
}
