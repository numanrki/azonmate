/**
 * ProductSearch – Reusable search component for Gutenberg blocks.
 *
 * @package AzonMate
 * @since   1.0.0
 */

import { __ } from '@wordpress/i18n';
import { TextControl, Button, Spinner } from '@wordpress/components';
import { useState } from '@wordpress/element';

export default function ProductSearch({ onSelect }) {
	const [keyword, setKeyword] = useState('');
	const [results, setResults] = useState([]);
	const [loading, setLoading] = useState(false);
	const [source, setSource] = useState('');

	const doFetch = (action, params) => {
		setLoading(true);
		const formData = new FormData();
		formData.append('action', action);
		formData.append('nonce', window.azonMateBlock?.nonce || '');
		Object.keys(params).forEach((k) => formData.append(k, params[k]));

		fetch(window.azonMateBlock?.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData,
		})
			.then((res) => res.json())
			.then((data) => {
				setLoading(false);
				if (data.success && data.data.products) {
					setResults(data.data.products);
					setSource(data.data.source || 'api');
				} else {
					setResults([]);
				}
			})
			.catch(() => {
				setLoading(false);
				setResults([]);
			});
	};

	const search = () => {
		if (!keyword.trim()) return;
		doFetch('azon_mate_search_products', { keywords: keyword });
	};

	const browseMyProducts = () => {
		doFetch('azon_mate_get_manual_products', { search: keyword });
	};

	return (
		<div className="azonmate-block-search">
			<div className="azonmate-block-search__input-row">
				<TextControl
					placeholder={__('Search products…', 'azonmate')}
					value={keyword}
					onChange={setKeyword}
					onKeyDown={(e) => e.key === 'Enter' && search()}
				/>
				<Button variant="primary" onClick={search} disabled={loading}>
					{loading ? <Spinner /> : __('Search', 'azonmate')}
				</Button>
				<Button variant="secondary" onClick={browseMyProducts} disabled={loading}>
					{__('My Products', 'azonmate')}
				</Button>
			</div>

			{results.length > 0 && (
				<div className="azonmate-block-search__results">
					{results.map((product) => {
						const imgUrl = product.image_medium || product.image_small || product.image_large || product.image || '';
						const price = product.price_display || product.price || '';
						return (
							<div
								key={product.asin}
								className="azonmate-block-search__result"
								onClick={() => onSelect(product)}
								role="button"
								tabIndex={0}
								onKeyDown={(e) => e.key === 'Enter' && onSelect(product)}
							>
								{imgUrl && <img src={imgUrl} alt="" />}
								<span className="azonmate-block-search__result-title">{product.title}</span>
								{price && (
									<span className="azonmate-block-search__result-price">{price}</span>
								)}
							</div>
						);
					})}
				</div>
			)}

			{results.length === 0 && !loading && source && (
				<p style={{ color: '#666', fontSize: '13px', textAlign: 'center', padding: '12px' }}>
					{__('No products found. Add products in AzonMate → Products.', 'azonmate')}
				</p>
			)}
		</div>
	);
}
