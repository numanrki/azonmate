/**
 * AzonMate Bestsellers – Block Entry Point
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, RangeControl, SelectControl, Placeholder } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType('azonmate/bestseller', {
	edit: function Edit({ attributes, setAttributes }) {
		const { keyword, items, template } = attributes;
		const blockProps = useBlockProps();

		if (!keyword) {
			return (
				<div {...blockProps}>
					<Placeholder
						icon="star-filled"
						label={__('AzonMate Bestsellers', 'azonmate')}
						instructions={__('Enter a category or keyword for bestsellers.', 'azonmate')}
					>
						<TextControl
							placeholder={__('e.g. Electronics, Headphones, Books', 'azonmate')}
							onChange={(val) => setAttributes({ keyword: val })}
						/>
					</Placeholder>
				</div>
			);
		}

		return (
			<div {...blockProps}>
				<InspectorControls>
					<PanelBody title={__('Bestseller Settings', 'azonmate')}>
						<TextControl
							label={__('Category / Keyword', 'azonmate')}
							value={keyword}
							onChange={(val) => setAttributes({ keyword: val })}
						/>
						<RangeControl
							label={__('Number of Items', 'azonmate')}
							value={items}
							onChange={(val) => setAttributes({ items: val })}
							min={1}
							max={10}
						/>
						<SelectControl
							label={__('Template', 'azonmate')}
							value={template}
							options={[
								{ label: __('Default', 'azonmate'), value: 'default' },
							]}
							onChange={(val) => setAttributes({ template: val })}
						/>
					</PanelBody>
				</InspectorControls>

				<ServerSideRender
					block="azonmate/bestseller"
					attributes={attributes}
				/>
			</div>
		);
	},
	save: () => null,
});
