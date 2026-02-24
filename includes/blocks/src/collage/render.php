<?php
/**
 * Server-side render callback for the Collage block.
 *
 * @package AzonMate\Blocks
 * @since   1.6.0
 */

// Abort if called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$registrar = \AzonMate\Blocks\BlockRegistrar::get_instance();
if ( $registrar ) {
	echo $registrar->render_block_collage( $attributes ?? array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
