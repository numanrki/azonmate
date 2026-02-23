<?php
/**
 * Server-side render for azonmate/product-box block.
 *
 * @package AzonMate\Blocks
 * @since   1.2.0
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content (empty for SSR).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$registrar = \AzonMate\Blocks\BlockRegistrar::get_instance();
echo $registrar->render_block_product_box( $attributes );
