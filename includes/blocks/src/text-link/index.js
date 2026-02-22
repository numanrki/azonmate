/**
 * AzonMate Text Link – Block Entry Point
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Placeholder } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType('azonmate/text-link', {
	edit: function Edit({ attributes, setAttributes }) {
		const { asin, title, text } = attributes;
		const blockProps = useBlockProps();

		if (!asin) {
			return (
				<div {...blockProps}>
					<Placeholder
						icon="admin-links"
						label={__('AzonMate Text Link', 'azonmate')}
						instructions={__('Enter a product ASIN.', 'azonmate')}
					>
						<TextControl
							placeholder="B08N5WRWNW"
							onChange={(val) => {
								if (/^[A-Z0-9]{10}$/i.test(val.trim())) {
									setAttributes({ asin: val.trim().toUpperCase() });
								}
							}}
						/>
					</Placeholder>
				</div>
			);
		}

		return (
			<div {...blockProps}>
				<InspectorControls>
					<PanelBody title={__('Link Settings', 'azonmate')}>
						<TextControl
							label={__('ASIN', 'azonmate')}
							value={asin}
							onChange={(val) => setAttributes({ asin: val })}
						/>
						<TextControl
							label={__('Link Text', 'azonmate')}
							value={text}
							onChange={(val) => setAttributes({ text: val })}
							help={__('Custom text for the link.', 'azonmate')}
						/>
						<TextControl
							label={__('Title Attribute', 'azonmate')}
							value={title}
							onChange={(val) => setAttributes({ title: val })}
						/>
					</PanelBody>
				</InspectorControls>

				<ServerSideRender
					block="azonmate/text-link"
					attributes={attributes}
				/>
			</div>
		);
	},
	save: () => null,
});
