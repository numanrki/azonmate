/**
 * ProductSelector – ASIN picker with search or direct input.
 *
 * @package AzonMate
 * @since   1.0.0
 */

import { __ } from '@wordpress/i18n';
import { TextControl, Button } from '@wordpress/components';
import { useState } from '@wordpress/element';
import ProductSearch from './ProductSearch';

export default function ProductSelector({ onSelect, label }) {
	const [mode, setMode] = useState('search'); // 'search' or 'manual'
	const [manualAsin, setManualAsin] = useState('');

	const handleManualSubmit = () => {
		const cleaned = manualAsin.trim().toUpperCase();
		if (/^[A-Z0-9]{10}$/.test(cleaned)) {
			onSelect({ asin: cleaned });
		}
	};

	return (
		<div className="azonmate-product-selector">
			<div style={{ display: 'flex', gap: '8px', marginBottom: '12px' }}>
				<Button
					variant={mode === 'search' ? 'primary' : 'secondary'}
					isSmall
					onClick={() => setMode('search')}
				>
					{__('Search', 'azonmate')}
				</Button>
				<Button
					variant={mode === 'manual' ? 'primary' : 'secondary'}
					isSmall
					onClick={() => setMode('manual')}
				>
					{__('Enter ASIN', 'azonmate')}
				</Button>
			</div>

			{mode === 'search' ? (
				<ProductSearch onSelect={onSelect} />
			) : (
				<div className="azonmate-block-search__input-row">
					<TextControl
						label={label || __('ASIN', 'azonmate')}
						placeholder="B08N5WRWNW"
						value={manualAsin}
						onChange={setManualAsin}
						onKeyDown={(e) => e.key === 'Enter' && handleManualSubmit()}
					/>
					<Button variant="primary" onClick={handleManualSubmit}>
						{__('Add', 'azonmate')}
					</Button>
				</div>
			)}
		</div>
	);
}
