/**
 * AzonMate Comparison Table – Block Entry Point
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, RangeControl, Placeholder } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType('azonmate/comparison-table', {
	edit: function Edit({ attributes, setAttributes }) {
		const { asins, columns, highlight, max, template } = attributes;
		const blockProps = useBlockProps();

		if (!asins) {
			return (
				<div {...blockProps}>
					<Placeholder
						icon="editor-table"
						label={__('AzonMate Comparison Table', 'azonmate')}
						instructions={__('Enter comma-separated ASINs to compare.', 'azonmate')}
					>
						<TextControl
							placeholder="B08N5WRWNW, B09V3KXJPB, B0BSHF7WHW"
							onChange={(val) => setAttributes({ asins: val })}
						/>
					</Placeholder>
				</div>
			);
		}

		return (
			<div {...blockProps}>
				<InspectorControls>
					<PanelBody title={__('Table Settings', 'azonmate')}>
						<TextControl
							label={__('ASINs (comma-separated)', 'azonmate')}
							value={asins}
							onChange={(val) => setAttributes({ asins: val })}
						/>
						<TextControl
							label={__('Columns', 'azonmate')}
							value={columns}
							onChange={(val) => setAttributes({ columns: val })}
							help={__('e.g. image,title,price,rating,prime,button. Leave empty for all.', 'azonmate')}
						/>
						<TextControl
							label={__('Highlight ASIN', 'azonmate')}
							value={highlight}
							onChange={(val) => setAttributes({ highlight: val })}
							help={__('ASIN of the product to highlight as "Best Pick".', 'azonmate')}
						/>
						<RangeControl
							label={__('Max Products', 'azonmate')}
							value={max}
							onChange={(val) => setAttributes({ max: val })}
							min={2}
							max={10}
						/>
					</PanelBody>
				</InspectorControls>

				<ServerSideRender
					block="azonmate/comparison-table"
					attributes={attributes}
				/>
			</div>
		);
	},
	save: () => null,
});
