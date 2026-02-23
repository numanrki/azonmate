/**
 * AzonMate Text Link – Block Entry Point
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
	Placeholder,
	Button,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState, createElement } from '@wordpress/element';

const icon = createElement(
	'svg',
	{ width: 24, height: 24, viewBox: '0 0 24 24', xmlns: 'http://www.w3.org/2000/svg' },
	createElement( 'rect', { x: 1, y: 1, width: 22, height: 22, rx: 4, fill: '#ff9900' } ),
	createElement( 'path', { d: 'M10 13a3 3 0 01-3-3 3 3 0 013-3l3 0M14 11a3 3 0 013 3 3 3 0 01-3 3h-3', stroke: '#fff', strokeWidth: 2, strokeLinecap: 'round', fill: 'none' } )
);

registerBlockType('azonmate/text-link', {
	icon,
	edit: function Edit({ attributes, setAttributes }) {
		const { asin, title, text } = attributes;
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
						icon={ icon }
						label={__('AzonMate Text Link', 'azonmate')}
						instructions={__('Search for a product or enter an ASIN.', 'azonmate')}
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
					<PanelBody title={__('Link Settings', 'azonmate')}>
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

				<div className="azonmate-editor-preview">
					<div className="azonmate-editor-preview__toolbar">
						<Button variant="tertiary" isSmall onClick={() => setAttributes({ asin: '' })}>
							{__('Replace', 'azonmate')}
						</Button>
					</div>
					<ServerSideRender block="azonmate/text-link" attributes={attributes} />
				</div>
			</div>
		);
	},
	save: () => null,
});
