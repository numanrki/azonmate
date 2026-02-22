<?php
/**
 * Comparison Table shortcode: [azonmate table="ASIN1,ASIN2,ASIN3"]
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
 * Class ComparisonTable
 *
 * @since 1.0.0
 */
class ComparisonTable extends AbstractShortcode {

	/**
	 * Available table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $available_columns = array(
		'image',
		'title',
		'price',
		'old_price',
		'rating',
		'prime',
		'features',
		'button',
	);

	/**
	 * Render the comparison table.
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
				'table'     => '',
				'columns'   => '',
				'highlight' => '',
				'max'       => 10,
				'template'  => 'default',
			),
			$atts,
			'azonmate'
		);

		$asins = $this->parse_asins( $atts['table'] );
		if ( empty( $asins ) ) {
			return '';
		}

		$asins    = array_slice( $asins, 0, absint( $atts['max'] ) );
		$products = $this->get_products( $asins );

		if ( empty( $products ) ) {
			return '';
		}

		// Parse columns.
		$columns = $this->available_columns;
		if ( ! empty( $atts['columns'] ) ) {
			$requested = array_map( 'trim', explode( ',', $atts['columns'] ) );
			$columns   = array_intersect( $requested, $this->available_columns );
			if ( empty( $columns ) ) {
				$columns = $this->available_columns;
			}
		}

		$data = array(
			'products'       => $products,
			'columns'        => $columns,
			'highlight_asin' => sanitize_text_field( $atts['highlight'] ),
			'template'       => $atts['template'],
		);

		return $this->renderer->render_comparison_table( $products, $data );
	}
}
