<?php
/**
 * Template: Text Link – Default
 *
 * @package AzonMate
 * @since   1.0.0
 *
 * @var \AzonMate\Models\Product|null $product Product object (may be null).
 * @var string                        $anchor  Anchor / display text.
 * @var string                        $asin    Product ASIN.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url = $product ? $product->get_detail_page_url() : '';

// Fallback URL if product not available.
if ( empty( $url ) ) {
	$marketplace = get_option( 'azon_mate_marketplace', 'www' );
	$tag         = get_option( 'azon_mate_partner_tag', '' );

	$domains = array(
		'www' => 'www.amazon.com',
		'uk'  => 'www.amazon.co.uk',
		'de'  => 'www.amazon.de',
		'fr'  => 'www.amazon.fr',
		'in'  => 'www.amazon.in',
		'ca'  => 'www.amazon.ca',
		'jp'  => 'www.amazon.co.jp',
		'it'  => 'www.amazon.it',
		'es'  => 'www.amazon.es',
		'au'  => 'www.amazon.com.au',
	);

	$domain = $domains[ $marketplace ] ?? $domains['www'];
	$url    = sprintf( 'https://%s/dp/%s?tag=%s', $domain, $asin, $tag );
}

$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
?>
<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	class="azonmate-text-link"
><?php echo wp_kses_post( $anchor ); ?></a>
