/**
 * AzonMate – Webpack Configuration
 *
 * Uses @wordpress/scripts defaults with custom entry points for each block.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/
 */

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	entry: {
		'product-box/index': path.resolve(
			__dirname,
			'includes/blocks/src/product-box/index.js'
		),
		'product-list/index': path.resolve(
			__dirname,
			'includes/blocks/src/product-list/index.js'
		),
		'comparison-table/index': path.resolve(
			__dirname,
			'includes/blocks/src/comparison-table/index.js'
		),
		'bestseller/index': path.resolve(
			__dirname,
			'includes/blocks/src/bestseller/index.js'
		),
		'text-link/index': path.resolve(
			__dirname,
			'includes/blocks/src/text-link/index.js'
		),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'build'),
	},
};
