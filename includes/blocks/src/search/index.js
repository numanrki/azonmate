/**
 * AzonMate Product Search – Block Entry Point
 *
 * Universal search block: search Amazon products, pick one, choose display type.
 *
 * @package AzonMate
 * @since   1.4.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	Placeholder,
	Button,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState } from '@wordpress/element';

registerBlockType('azonmate/search', {
	edit: function Edit({ attributes, setAttributes }) {
		const { asin, displayType, template } = attributes;
		const blockProps = useBlockProps();
		const [searchKeyword, setSearchKeyword] = useState('');
		const [searchResults, setSearchResults] = useState([]);
		const [isSearching, setIsSearching] = useState(false);

		const doSearch = () => {
			if (!searchKeyword.trim()) return;
			setIsSearching(true);
			const fd = new FormData();
			fd.append('action', 'azon_mate_search_products');
			fd.append('nonce', window.azonMateBlock?.nonce || '');
			fd.append('keywords', searchKeyword);
			fetch(window.azonMateBlock?.ajaxUrl || '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: fd,
			})
				.then((r) => r.json())
				.then((data) => {
					setIsSearching(false);
					if (data.success && data.data.products) {
						setSearchResults(data.data.products);
					}
				})
				.catch(() => setIsSearching(false));
		};

		const browseMyProducts = () => {
			setIsSearching(true);
			const fd = new FormData();
			fd.append('action', 'azon_mate_get_manual_products');
			fd.append('nonce', window.azonMateBlock?.nonce || '');
			fd.append('search', searchKeyword);
			fetch(window.azonMateBlock?.ajaxUrl || '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: fd,
			})
				.then((r) => r.json())
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
						icon="search"
						label={__('AzonMate Product Search', 'azonmate')}
						instructions={__(
							'Search Amazon or browse your saved products. Select a product and choose how to display it.',
							'azonmate'
						)}
					>
						<div className="azonmate-block-search">
							<div className="azonmate-block-search__input-row">
								<TextControl
									placeholder={__('Search Amazon products…', 'azonmate')}
									value={searchKeyword}
									onChange={setSearchKeyword}
									onKeyDown={(e) => e.key === 'Enter' && doSearch()}
								/>
								<Button variant="primary" onClick={doSearch} disabled={isSearching}>
									{isSearching ? <Spinner /> : __('Search', 'azonmate')}
								</Button>
								<Button variant="secondary" onClick={browseMyProducts} disabled={isSearching}>
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

							<div className="azonmate-block-search__display-type">
								<SelectControl
									label={__('Display as', 'azonmate')}
									value={displayType}
									options={[
										{ label: __('Product Box', 'azonmate'), value: 'box' },
										{ label: __('Text Link', 'azonmate'), value: 'link' },
										{ label: __('Image Link', 'azonmate'), value: 'image' },
									]}
									onChange={(val) => setAttributes({ displayType: val })}
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
											onKeyDown={(e) =>
												e.key === 'Enter' && selectProduct(product.asin)
											}
										>
											{(product.image_medium ||
												product.image_small ||
												product.image) && (
												<img
													src={
														product.image_medium ||
														product.image_small ||
														product.image
													}
													alt=""
												/>
											)}
											<span className="azonmate-block-search__result-title">
												{product.title}
											</span>
											{(product.price_display || product.price) && (
												<span className="azonmate-block-search__result-price">
													{product.price_display || product.price}
												</span>
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
						<SelectControl
							label={__('Display Type', 'azonmate')}
							value={displayType}
							options={[
								{ label: __('Product Box', 'azonmate'), value: 'box' },
								{ label: __('Text Link', 'azonmate'), value: 'link' },
								{ label: __('Image Link', 'azonmate'), value: 'image' },
							]}
							onChange={(val) => setAttributes({ displayType: val })}
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
					<ServerSideRender block="azonmate/search" attributes={attributes} />
				</div>
			</div>
		);
	},
	save: () => null,
});
