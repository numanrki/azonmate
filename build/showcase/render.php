<?php
/**
 * Server-side render callback for the Showcase block.
 *
 * @package AzonMate\Blocks
 * @since   1.5.0
 */

// Abort if called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$registrar = \AzonMate\Blocks\BlockRegistrar::get_instance();
if ( $registrar ) {
	echo $registrar->render_block_showcase( $attributes ?? array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
