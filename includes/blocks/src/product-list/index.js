/**
 * AzonMate Product List – Block Entry Point
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, RangeControl, Placeholder } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType('azonmate/product-list', {
	edit: function Edit({ attributes, setAttributes }) {
		const { asins, template, max } = attributes;
		const blockProps = useBlockProps();

		if (!asins) {
			return (
				<div {...blockProps}>
					<Placeholder
						icon="list-view"
						label={__('AzonMate Product List', 'azonmate')}
						instructions={__('Enter comma-separated ASINs to display.', 'azonmate')}
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
					<PanelBody title={__('List Settings', 'azonmate')}>
						<TextControl
							label={__('ASINs (comma-separated)', 'azonmate')}
							value={asins}
							onChange={(val) => setAttributes({ asins: val })}
						/>
						<SelectControl
							label={__('Template', 'azonmate')}
							value={template}
							options={[
								{ label: __('Default (Vertical)', 'azonmate'), value: 'default' },
								{ label: __('Grid', 'azonmate'), value: 'grid' },
							]}
							onChange={(val) => setAttributes({ template: val })}
						/>
						<RangeControl
							label={__('Max Products', 'azonmate')}
							value={max}
							onChange={(val) => setAttributes({ max: val })}
							min={1}
							max={20}
						/>
					</PanelBody>
				</InspectorControls>

				<ServerSideRender
					block="azonmate/product-list"
					attributes={attributes}
				/>
			</div>
		);
	},
	save: () => null,
});
