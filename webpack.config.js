/**
 * AzonMate – Webpack Configuration
 *
 * Uses @wordpress/scripts defaults with custom entry points for each block.
 * Copies block.json and render.php into the build output directories.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/
 */

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');

const blocks = [
	'product-box',
	'product-list',
	'comparison-table',
	'bestseller',
	'text-link',
	'search',
	'showcase',
	'collage',
];

const entry = {};
blocks.forEach((slug) => {
	entry[`${slug}/index`] = path.resolve(
		__dirname,
		`includes/blocks/src/${slug}/index.js`
	);
});

module.exports = {
	...defaultConfig,
	entry,
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'build'),
	},
	plugins: [
		...(defaultConfig.plugins || []),
		new CopyPlugin({
			patterns: [
				// Copy editor.css to build root.
				{
					from: path.resolve(
						__dirname,
						'includes/blocks/src/editor.css'
					),
					to: path.resolve(__dirname, 'build/editor.css'),
					noErrorOnMissing: true,
				},
				// Per-block: copy block.json and render.php.
				...blocks
					.map((slug) => {
						const srcDir = path.resolve(
							__dirname,
							`includes/blocks/src/${slug}`
						);
						return [
							{
								from: path.resolve(srcDir, 'block.json'),
								to: path.resolve(
									__dirname,
									`build/${slug}/block.json`
								),
								noErrorOnMissing: true,
							},
							{
								from: path.resolve(srcDir, 'render.php'),
								to: path.resolve(
									__dirname,
									`build/${slug}/render.php`
								),
								noErrorOnMissing: true,
							},
						];
					})
					.flat(),
			],
		}),
	],
};
