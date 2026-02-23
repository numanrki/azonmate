/**
 * AzonMate Product Box – Editor Component
 *
 * @package AzonMate
 * @since   1.0.0
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	SelectControl,
	Button,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const azonIcon = wp.element.createElement(
	'svg',
	{ width: 24, height: 24, viewBox: '0 0 24 24', xmlns: 'http://www.w3.org/2000/svg' },
	wp.element.createElement( 'rect', { x: 1, y: 1, width: 22, height: 22, rx: 4, fill: '#ff9900' } ),
	wp.element.createElement( 'path', { d: 'M7 8h10M7 12h10M7 16h6', stroke: '#fff', strokeWidth: 2, strokeLinecap: 'round', fill: 'none' } )
);

export default function Edit({ attributes, setAttributes }) {
	const { asin, template, title, description, rating, price, buttonText, imageSize } = attributes;
	const blockProps = useBlockProps();
	const [searchKeyword, setSearchKeyword] = useState('');
	const [searchResults, setSearchResults] = useState([]);
	const [isSearching, setIsSearching] = useState(false);

	const doSearch = () => {
		if (!searchKeyword.trim()) return;

		setIsSearching(true);
		const formData = new FormData();
		formData.append('action', 'azon_mate_search_products');
		formData.append('nonce', window.azonMateBlock?.nonce || '');
		formData.append('keywords', searchKeyword);

		fetch(window.azonMateBlock?.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData,
		})
			.then((res) => res.json())
			.then((data) => {
				setIsSearching(false);
				if (data.success && data.data.products) {
					setSearchResults(data.data.products);
				}
			})
			.catch(() => setIsSearching(false));
	};

	const selectProduct = (selectedAsin) => {
		setAttributes({ asin: selectedAsin });
		setSearchResults([]);
		setSearchKeyword('');
	};

	if (!asin) {
		return (
			<div {...blockProps}>
				<Placeholder
					icon={ azonIcon }
					label={__('AzonMate Product Box', 'azonmate')}
					instructions={__('Search for an Amazon product or enter an ASIN.', 'azonmate')}
				>
					<div className="azonmate-block-search">
						<div className="azonmate-block-search__input-row">
							<TextControl
								placeholder={__('Search products or enter ASIN…', 'azonmate')}
								value={searchKeyword}
								onChange={setSearchKeyword}
								onKeyDown={(e) => e.key === 'Enter' && doSearch()}
							/>
							<Button variant="primary" onClick={doSearch} disabled={isSearching}>
								{isSearching ? <Spinner /> : __('Search', 'azonmate')}
							</Button>
							<Button variant="secondary" onClick={() => {
								setIsSearching(true);
								const fd = new FormData();
								fd.append('action', 'azon_mate_get_manual_products');
								fd.append('nonce', window.azonMateBlock?.nonce || '');
								fd.append('search', searchKeyword);
								fetch(window.azonMateBlock?.ajaxUrl || '/wp-admin/admin-ajax.php', {
									method: 'POST',
									body: fd,
								}).then(r => r.json()).then(data => {
									setIsSearching(false);
									if (data.success && data.data.products) {
										setSearchResults(data.data.products);
									}
								}).catch(() => setIsSearching(false));
							}} disabled={isSearching}>
								{__('My Products', 'azonmate')}
							</Button>
						</div>

						<div className="azonmate-block-search__input-row">
							<TextControl
								placeholder={__('Or paste ASIN directly…', 'azonmate')}
								onChange={(val) => {
									if (/^[A-Z0-9]{10}$/i.test(val.trim())) {
										selectProduct(val.trim().toUpperCase());
									}
								}}
							/>
						</div>

						{searchResults.length > 0 && (
							<div className="azonmate-block-search__results">
								{searchResults.map((product) => (
									<div
										key={product.asin}
										className="azonmate-block-search__result"
										onClick={() => selectProduct(product.asin)}
										role="button"
										tabIndex={0}
										onKeyDown={(e) => e.key === 'Enter' && selectProduct(product.asin)}
									>
										{(product.image_medium || product.image_small || product.image_large || product.image) && <img src={product.image_medium || product.image_small || product.image_large || product.image} alt="" />}
										<span className="azonmate-block-search__result-title">{product.title}</span>
										{(product.price_display || product.price) && (
											<span className="azonmate-block-search__result-price">{product.price_display || product.price}</span>
										)}
									</div>
								))}
							</div>
						)}
					</div>
				</Placeholder>
			</div>
		);
	}

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Product Settings', 'azonmate')}>
					<TextControl
						label={__('ASIN', 'azonmate')}
						value={asin}
						onChange={(val) => setAttributes({ asin: val })}
					/>
					<Button
						variant="secondary"
						isSmall
						onClick={() => setAttributes({ asin: '' })}
						style={{ marginBottom: '16px' }}
					>
						{__('Replace Product', 'azonmate')}
					</Button>
					<TextControl
						label={__('Custom Title', 'azonmate')}
						value={title}
						onChange={(val) => setAttributes({ title: val })}
						help={__('Leave empty to use the product title from Amazon.', 'azonmate')}
					/>
					<SelectControl
						label={__('Template', 'azonmate')}
						value={template}
						options={[
							{ label: __('Default', 'azonmate'), value: 'default' },
							{ label: __('Horizontal', 'azonmate'), value: 'horizontal' },
							{ label: __('Compact', 'azonmate'), value: 'compact' },
						]}
						onChange={(val) => setAttributes({ template: val })}
					/>
					<SelectControl
						label={__('Image Size', 'azonmate')}
						value={imageSize}
						options={[
							{ label: __('Small', 'azonmate'), value: 'small' },
							{ label: __('Medium', 'azonmate'), value: 'medium' },
							{ label: __('Large', 'azonmate'), value: 'large' },
						]}
						onChange={(val) => setAttributes({ imageSize: val })}
					/>
				</PanelBody>
				<PanelBody title={__('Display Options', 'azonmate')} initialOpen={false}>
					<ToggleControl
						label={__('Show Description', 'azonmate')}
						checked={description}
						onChange={(val) => setAttributes({ description: val })}
					/>
					<ToggleControl
						label={__('Show Rating', 'azonmate')}
						checked={rating}
						onChange={(val) => setAttributes({ rating: val })}
					/>
					<ToggleControl
						label={__('Show Price', 'azonmate')}
						checked={price}
						onChange={(val) => setAttributes({ price: val })}
					/>
					<TextControl
						label={__('Button Text', 'azonmate')}
						value={buttonText}
						onChange={(val) => setAttributes({ buttonText: val })}
						help={__('Leave empty for default.', 'azonmate')}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="azonmate-editor-preview">
				<div className="azonmate-editor-preview__toolbar">
					<Button
						variant="tertiary"
						isSmall
						onClick={() => setAttributes({ asin: '' })}
					>
						{__('Replace', 'azonmate')}
					</Button>
				</div>
				<ServerSideRender
					block="azonmate/product-box"
					attributes={attributes}
				/>
			</div>
		</div>
	);
}
