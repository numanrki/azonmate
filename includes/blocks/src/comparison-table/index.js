/**
 * AzonMate Comparison Table – Block Entry Point
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
	Placeholder,
	Button,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState } from '@wordpress/element';

registerBlockType('azonmate/comparison-table', {
	edit: function Edit({ attributes, setAttributes }) {
		const { asins, columns, highlight, max, template } = attributes;
		const blockProps = useBlockProps();
		const [searchKeyword, setSearchKeyword] = useState('');
		const [searchResults, setSearchResults] = useState([]);
		const [isSearching, setIsSearching] = useState(false);
		const [selectedProducts, setSelectedProducts] = useState(
			asins ? asins.split(',').map((a) => a.trim()).filter(Boolean) : []
		);

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

		const addProduct = (asin) => {
			if (selectedProducts.includes(asin)) return;
			const updated = [...selectedProducts, asin];
			setSelectedProducts(updated);
			setAttributes({ asins: updated.join(',') });
		};

		const removeProduct = (asin) => {
			const updated = selectedProducts.filter((a) => a !== asin);
			setSelectedProducts(updated);
			setAttributes({ asins: updated.join(',') });
		};

		if (!asins) {
			return (
				<div {...blockProps}>
					<Placeholder
						icon="editor-table"
						label={__('AzonMate Comparison Table', 'azonmate')}
						instructions={__(
							'Search and add products to compare, or enter comma-separated ASINs.',
							'azonmate'
						)}
					>
						<div className="azonmate-block-search">
							<div className="azonmate-block-search__input-row">
								<TextControl
									placeholder={__('Search products…', 'azonmate')}
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
									placeholder={__('Or paste ASINs: B08N5WRWNW, B09V3KXJPB', 'azonmate')}
									onChange={(val) => {
										if (val.trim()) {
											setAttributes({ asins: val.trim() });
										}
									}}
								/>
							</div>

							{selectedProducts.length > 0 && (
								<div className="azonmate-block-search__selected">
									<strong>{__('Selected:', 'azonmate')}</strong>
									{selectedProducts.map((a) => (
										<span key={a} className="azonmate-block-search__tag">
											{a}
											<button type="button" onClick={() => removeProduct(a)}>&times;</button>
										</span>
									))}
									<Button
										variant="primary"
										isSmall
										onClick={() => setAttributes({ asins: selectedProducts.join(',') })}
									>
										{__('Done', 'azonmate')}
									</Button>
								</div>
							)}

							{searchResults.length > 0 && (
								<div className="azonmate-block-search__results">
									{searchResults.map((product) => (
										<div
											key={product.asin}
											className="azonmate-block-search__result"
											onClick={() => addProduct(product.asin)}
											role="button"
											tabIndex={0}
											onKeyDown={(e) => e.key === 'Enter' && addProduct(product.asin)}
										>
											{(product.image_medium || product.image_small || product.image) && (
												<img src={product.image_medium || product.image_small || product.image} alt="" />
											)}
											<span className="azonmate-block-search__result-title">{product.title}</span>
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
					<PanelBody title={__('Table Settings', 'azonmate')}>
						<TextControl
							label={__('ASINs (comma-separated)', 'azonmate')}
							value={asins}
							onChange={(val) => setAttributes({ asins: val })}
						/>
						<Button
							variant="secondary"
							isSmall
							onClick={() => setAttributes({ asins: '' })}
							style={{ marginBottom: '16px' }}
						>
							{__('Replace Products', 'azonmate')}
						</Button>
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

				<div className="azonmate-editor-preview">
					<div className="azonmate-editor-preview__toolbar">
						<Button variant="tertiary" isSmall onClick={() => setAttributes({ asins: '' })}>
							{__('Replace', 'azonmate')}
						</Button>
					</div>
					<ServerSideRender block="azonmate/comparison-table" attributes={attributes} />
				</div>
			</div>
		);
	},
	save: () => null,
});
