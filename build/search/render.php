<?php
/**
 * Server-side render for azonmate/search block.
 *
 * @package AzonMate\Blocks
 * @since   1.4.0
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$registrar = \AzonMate\Blocks\BlockRegistrar::get_instance();
echo $registrar->render_block_search( $attributes );
