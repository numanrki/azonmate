<?php
/**
 * Bestseller / New Releases shortcode.
 *
 * [azonmate bestseller="Category" items="10"]
 * [azonmate new_releases="Category" items="5"]
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
 * Class Bestseller
 *
 * @since 1.0.0
 */
class Bestseller extends AbstractShortcode {

	/**
	 * Render bestseller or new releases list.
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
				'bestseller'   => '',
				'new_releases' => '',
				'items'        => 10,
				'template'     => 'default',
				'_sort'        => '', // Internal flag for new_releases.
			),
			$atts,
			'azonmate'
		);

		$keyword = ! empty( $atts['bestseller'] )
			? sanitize_text_field( $atts['bestseller'] )
			: sanitize_text_field( $atts['new_releases'] );

		if ( empty( $keyword ) ) {
			return '';
		}

		$items  = absint( $atts['items'] );
		$items  = min( max( $items, 1 ), 10 ); // PA-API max 10 per request.

		$sort_by = ( 'newest' === $atts['_sort'] ) ? 'NewestArrivals' : 'Relevance';

		// Determine if keyword matches a known category.
		$search_index = $this->resolve_search_index( $keyword );

		$search_args = array(
			'Keywords'    => $keyword,
			'ItemCount'   => $items,
			'SortBy'      => $sort_by,
		);

		if ( $search_index ) {
			$search_args['SearchIndex'] = $search_index;
		}

		$products = $this->api->search_items( $search_args );

		if ( empty( $products ) || is_wp_error( $products ) ) {
			return '';
		}

		$data = array(
			'products' => $products,
			'keyword'  => $keyword,
			'template' => $atts['template'],
		);

		return $this->renderer->render_bestseller_list( $products, $data );
	}

	/**
	 * Try to map a keyword to an Amazon SearchIndex.
	 *
	 * @since 1.0.0
	 *
	 * @param string $keyword Category name or keyword.
	 * @return string|null SearchIndex or null.
	 */
	private function resolve_search_index( $keyword ) {
		$map = array(
			'all'                    => 'All',
			'arts'                   => 'ArtsAndCrafts',
			'automotive'             => 'Automotive',
			'baby'                   => 'Baby',
			'beauty'                 => 'Beauty',
			'books'                  => 'Books',
			'clothing'               => 'Fashion',
			'fashion'                => 'Fashion',
			'computers'              => 'Computers',
			'electronics'            => 'Electronics',
			'garden'                 => 'GardenAndOutdoor',
			'grocery'                => 'GroceryAndGourmetFood',
			'health'                 => 'HealthAndHousehold',
			'home'                   => 'HomeAndKitchen',
			'kitchen'                => 'HomeAndKitchen',
			'industrial'             => 'Industrial',
			'kindle'                 => 'KindleStore',
			'luggage'                => 'Luggage',
			'movies'                 => 'MoviesAndTV',
			'music'                  => 'Music',
			'office'                 => 'OfficeProducts',
			'outdoors'               => 'SportsAndOutdoors',
			'pets'                   => 'PetSupplies',
			'software'               => 'Software',
			'sports'                 => 'SportsAndOutdoors',
			'tools'                  => 'ToolsAndHomeImprovement',
			'toys'                   => 'ToysAndGames',
			'video games'            => 'VideoGames',
			'videogames'             => 'VideoGames',
			'headphones'             => 'Electronics',
			'laptops'                => 'Computers',
			'smartphones'            => 'Electronics',
		);

		$lower = strtolower( trim( $keyword ) );

		return $map[ $lower ] ?? null;
	}
}
