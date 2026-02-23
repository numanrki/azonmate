/**
 * AzonMate Product Box – Block Entry Point
 *
 * @package AzonMate
 * @since   1.0.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import Edit from './edit';

const icon = createElement(
	'svg',
	{ width: 24, height: 24, viewBox: '0 0 24 24', xmlns: 'http://www.w3.org/2000/svg' },
	createElement( 'rect', { x: 1, y: 1, width: 22, height: 22, rx: 4, fill: '#ff9900' } ),
	createElement( 'path', { d: 'M7 8h10M7 12h10M7 16h6', stroke: '#fff', strokeWidth: 2, strokeLinecap: 'round', fill: 'none' } )
);

registerBlockType('azonmate/product-box', {
	icon,
	edit: Edit,
	save: () => null,
});
