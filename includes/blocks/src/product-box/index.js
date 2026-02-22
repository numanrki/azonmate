/**
 * AzonMate Product Box – Block Entry Point
 *
 * @package AzonMate
 * @since   1.0.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';

registerBlockType('azonmate/product-box', {
	edit: Edit,
	save: () => null, // Server-side rendered.
});
