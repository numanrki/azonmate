<?php
/**
 * Template helper functions.
 *
 * @package AzonMate\Templates
 * @since   1.0.0
 */

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build link attributes for product links.
 *
 * @since 1.0.0
 *
 * @param string $url     The product URL.
 * @param array  $options Display options with 'new_tab' and 'nofollow' keys.
 * @return string HTML attributes string.
 */
function azon_mate_link_attributes( $url, $options = array() ) {
	// Support passing ASIN string as second arg (used by templates).
	if ( is_string( $options ) ) {
		$options = array( 'asin' => $options );
	}

	$new_tab  = isset( $options['new_tab'] ) ? $options['new_tab'] : ( '1' === get_option( 'azon_mate_open_new_tab', '1' ) );
	$nofollow = isset( $options['nofollow'] ) ? $options['nofollow'] : ( '1' === get_option( 'azon_mate_nofollow_links', '1' ) );

	$attrs = 'href="' . esc_url( $url ) . '"';

	if ( $new_tab ) {
		$attrs .= ' target="_blank"';
	}

	$rel_parts = array();
	if ( $nofollow ) {
		$rel_parts[] = 'nofollow';
		$rel_parts[] = 'sponsored';
	}
	if ( $new_tab ) {
		$rel_parts[] = 'noopener';
	}

	if ( ! empty( $rel_parts ) ) {
		$attrs .= ' rel="' . esc_attr( implode( ' ', $rel_parts ) ) . '"';
	}

	return $attrs;
}

/**
 * Render star rating HTML.
 *
 * @since 1.0.0
 *
 * @param float $rating      Star rating (0.0-5.0).
 * @param int   $review_count Number of reviews.
 * @return string HTML output.
 */
function azon_mate_render_stars( $rating, $review_count = 0 ) {
	if ( empty( $rating ) ) {
		return '';
	}

	$rating = round( $rating, 1 );
	$full   = floor( $rating );
	$half   = ( $rating - $full ) >= 0.5 ? 1 : 0;
	$empty  = 5 - $full - $half;

	$html = '<div class="azonmate-stars" title="' . esc_attr( $rating . '/5' ) . '">';

	for ( $i = 0; $i < $full; $i++ ) {
		$html .= '<span class="azonmate-star azonmate-star--full">★</span>';
	}
	if ( $half ) {
		$html .= '<span class="azonmate-star azonmate-star--half">★</span>';
	}
	for ( $i = 0; $i < $empty; $i++ ) {
		$html .= '<span class="azonmate-star azonmate-star--empty">☆</span>';
	}

	if ( $review_count > 0 ) {
		$html .= ' <span class="azonmate-review-count">(' . number_format_i18n( $review_count ) . ')</span>';
	}

	$html .= '</div>';

	return $html;
}

/**
 * Render the Prime badge HTML.
 *
 * @since 1.0.0
 *
 * @param bool $is_prime Whether the product is Prime eligible.
 * @return string HTML output.
 */
function azon_mate_render_prime_badge( $is_prime = true ) {
	if ( ! $is_prime ) {
		return '';
	}

	return '<span class="azonmate-prime-badge" title="' . esc_attr__( 'Amazon Prime', 'azonmate' ) . '">
		<svg class="azonmate-prime-icon" width="50" height="16" viewBox="0 0 70 16" xmlns="http://www.w3.org/2000/svg">
			<text x="0" y="13" fill="#00A8E1" font-family="Arial, sans-serif" font-size="12" font-weight="bold">prime</text>
		</svg>
	</span>';
}

/**
 * Get the buy button HTML.
 *
 * @since 1.0.0
 *
 * @param string $url         Product URL.
 * @param string $button_text Button text.
 * @param array  $options     Display options.
 * @return string HTML output.
 */
function azon_mate_render_buy_button( $url, $button_text = '', $options = array() ) {
	// Support passing ASIN string as third arg (used by templates).
	if ( is_string( $options ) ) {
		$options = array( 'asin' => $options );
	}

	if ( empty( $button_text ) ) {
		$button_text = get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' );
	}

	$link_attrs = azon_mate_link_attributes( $url, $options );

	return sprintf(
		'<a %s class="azonmate-buy-btn" data-asin="%s">%s</a>',
		$link_attrs,
		esc_attr( $options['asin'] ?? '' ),
		esc_html( $button_text )
	);
}

/**
 * Render the savings badge.
 *
 * @since 1.0.0
 *
 * @param int $savings_percentage Savings percentage.
 * @return string HTML output.
 */
function azon_mate_render_savings_badge( $savings_percentage ) {
	if ( empty( $savings_percentage ) || $savings_percentage <= 0 ) {
		return '';
	}

	return sprintf(
		'<span class="azonmate-savings-badge">-%d%%</span>',
		absint( $savings_percentage )
	);
}

/**
 * Render the Amazon affiliate disclosure notice.
 *
 * Outputs once per showcase block when enabled in settings.
 *
 * @since 1.3.1
 *
 * @return string HTML output or empty string.
 */
function azon_mate_render_disclosure() {
	$show = get_option( 'azon_mate_show_disclosure', '1' );

	if ( '1' !== (string) $show ) {
		return '';
	}

	return '<p class="azonmate-showcase__disclosure">'
		. esc_html__( 'As an Amazon Associate, I earn from qualifying purchases.', 'azonmate' )
		. '</p>';
}
