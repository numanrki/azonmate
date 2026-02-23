/**
 * AzonMate Bestsellers – Block Entry Point
 *
 * @package AzonMate
 * @since   1.0.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	SelectControl,
	Placeholder,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState, createElement } from '@wordpress/element';

const icon = createElement(
	'svg',
	{ width: 24, height: 24, viewBox: '0 0 24 24', xmlns: 'http://www.w3.org/2000/svg' },
	createElement( 'rect', { x: 1, y: 1, width: 22, height: 22, rx: 4, fill: '#ff9900' } ),
	createElement( 'path', { d: 'M12 4l2.5 5 5.5.8-4 3.9.9 5.3L12 16.8 7.1 19l.9-5.3-4-3.9 5.5-.8z', fill: '#fff' } )
);

const POPULAR_CATEGORIES = [
	'Electronics',
	'Books',
	'Home & Kitchen',
	'Clothing',
	'Sports & Outdoors',
	'Toys & Games',
	'Health & Household',
	'Beauty & Personal Care',
	'Automotive',
	'Tools & Home Improvement',
];

registerBlockType('azonmate/bestseller', {
	icon,
	edit: function Edit({ attributes, setAttributes }) {
		const { keyword, items, template } = attributes;
		const blockProps = useBlockProps();
		const [customKeyword, setCustomKeyword] = useState('');

		if (!keyword) {
			return (
				<div {...blockProps}>
					<Placeholder
						icon={ icon }
						label={__('AzonMate Bestsellers', 'azonmate')}
						instructions={__(
							'Pick a category or enter a custom keyword for bestsellers.',
							'azonmate'
						)}
					>
						<div className="azonmate-block-search">
							<div className="azonmate-block-search__input-row">
								<TextControl
									placeholder={__('Custom keyword, e.g. Wireless Headphones', 'azonmate')}
									value={customKeyword}
									onChange={setCustomKeyword}
									onKeyDown={(e) => {
										if (e.key === 'Enter' && customKeyword.trim()) {
											setAttributes({ keyword: customKeyword.trim() });
										}
									}}
								/>
								<Button
									variant="primary"
									onClick={() => {
										if (customKeyword.trim()) {
											setAttributes({ keyword: customKeyword.trim() });
										}
									}}
								>
									{__('Go', 'azonmate')}
								</Button>
							</div>
							<div className="azonmate-block-search__categories">
								{POPULAR_CATEGORIES.map((cat) => (
									<Button
										key={cat}
										variant="secondary"
										isSmall
										onClick={() => setAttributes({ keyword: cat })}
										style={{ margin: '4px' }}
									>
										{cat}
									</Button>
								))}
							</div>
						</div>
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
						<Button
							variant="secondary"
							isSmall
							onClick={() => setAttributes({ keyword: '' })}
							style={{ marginBottom: '16px' }}
						>
							{__('Change Category', 'azonmate')}
						</Button>
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

				<div className="azonmate-editor-preview">
					<div className="azonmate-editor-preview__toolbar">
						<Button variant="tertiary" isSmall onClick={() => setAttributes({ keyword: '' })}>
							{__('Change', 'azonmate')}
						</Button>
					</div>
					<ServerSideRender block="azonmate/bestseller" attributes={attributes} />
				</div>
			</div>
		);
	},
	save: () => null,
});
