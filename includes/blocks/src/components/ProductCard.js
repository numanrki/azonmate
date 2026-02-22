/**
 * ProductCard – Preview component for the editor.
 *
 * @package AzonMate
 * @since   1.0.0
 */

import { __ } from '@wordpress/i18n';

export default function ProductCard({ product }) {
	if (!product) {
		return null;
	}

	const imgUrl = product.image_medium || product.image_small || product.image_large || product.image || '';
	const price = product.price_display || product.price || '';

	return (
		<div className="azonmate-editor-product-card">
			{imgUrl && (
				<div className="azonmate-editor-product-card__image">
					<img src={imgUrl} alt={product.title || ''} />
				</div>
			)}
			<div className="azonmate-editor-product-card__info">
				<strong>{product.title}</strong>
				{price && <span className="azonmate-editor-product-card__price">{price}</span>}
				{product.rating && <span className="azonmate-editor-product-card__rating">★ {product.rating}</span>}
				<small className="azonmate-editor-product-card__asin">ASIN: {product.asin}</small>
			</div>
		</div>
	);
}
