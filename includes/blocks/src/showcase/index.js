/**
 * AzonMate Showcase – Block Entry Point
 *
 * Step 1: Pick a layout from 8 pre-built designs.
 * Step 2: Search / add products.
 * Step 3: Live server-side preview.
 *
 * @package AzonMate
 * @since   1.5.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	SelectControl,
	ToggleControl,
	Placeholder,
	Button,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState } from '@wordpress/element';

/* ------------------------------------------------------------------ */
/*  Layout catalogue – 8 pre-built showcase designs                   */
/* ------------------------------------------------------------------ */
const LAYOUTS = [
	{
		value: 'grid',
		label: __( 'Grid Cards', 'azonmate' ),
		desc: __( 'Responsive card grid — best for 3-12 products', 'azonmate' ),
		type: 'multi',
		icon: '▦',
	},
	{
		value: 'list',
		label: __( 'List / Row', 'azonmate' ),
		desc: __( 'Horizontal row layout — great for 2-6 products', 'azonmate' ),
		type: 'multi',
		icon: '☰',
	},
	{
		value: 'masonry',
		label: __( 'Masonry / Collage', 'azonmate' ),
		desc: __( 'Pinterest-style collage — ideal for varied images', 'azonmate' ),
		type: 'multi',
		icon: '⊞',
	},
	{
		value: 'table',
		label: __( 'Comparison Table', 'azonmate' ),
		desc: __( 'Side-by-side spec table — perfect for comparing', 'azonmate' ),
		type: 'multi',
		icon: '⊟',
	},
	{
		value: 'hero',
		label: __( 'Hero Card', 'azonmate' ),
		desc: __( 'Large featured card — spotlight a single product', 'azonmate' ),
		type: 'single',
		icon: '◆',
	},
	{
		value: 'compact',
		label: __( 'Compact Inline', 'azonmate' ),
		desc: __( 'Slim inline card — fits inside article text', 'azonmate' ),
		type: 'single',
		icon: '▬',
	},
	{
		value: 'split',
		label: __( 'Split Layout', 'azonmate' ),
		desc: __( '50/50 image + details panel — editorial style', 'azonmate' ),
		type: 'single',
		icon: '◧',
	},
	{
		value: 'deal',
		label: __( 'Deal Card', 'azonmate' ),
		desc: __( 'Price-focused card — emphasis on savings', 'azonmate' ),
		type: 'single',
		icon: '💰',
	},
];

/* ------------------------------------------------------------------ */
/*  Brand icon (used in Placeholder)                                  */
/* ------------------------------------------------------------------ */
const showcaseIcon = wp.element.createElement(
	'svg',
	{ width: 24, height: 24, viewBox: '0 0 24 24', xmlns: 'http://www.w3.org/2000/svg' },
	wp.element.createElement( 'rect', { x: 1, y: 1, width: 22, height: 22, rx: 4, fill: '#ff9900' } ),
	wp.element.createElement( 'rect', { x: 4, y: 4, width: 7, height: 7, rx: 1.5, fill: '#fff' } ),
	wp.element.createElement( 'rect', { x: 13, y: 4, width: 7, height: 7, rx: 1.5, fill: '#fff' } ),
	wp.element.createElement( 'rect', { x: 4, y: 13, width: 7, height: 7, rx: 1.5, fill: '#fff' } ),
	wp.element.createElement( 'rect', { x: 13, y: 13, width: 7, height: 7, rx: 1.5, fill: '#fff' } )
);

registerBlockType( 'azonmate/showcase', {
	icon: showcaseIcon,
	edit: function Edit( { attributes, setAttributes } ) {
		const { asins, layout, columns, max, heading, showBadge, showPrice, showRating, buttonText } = attributes;
		const blockProps = useBlockProps();

		/* ---- state ---- */
		const [ searchKeyword, setSearchKeyword ] = useState( '' );
		const [ searchResults, setSearchResults ] = useState( [] );
		const [ isSearching, setIsSearching ] = useState( false );
		const [ selectedProducts, setSelectedProducts ] = useState(
			asins ? asins.split( ',' ).map( ( a ) => a.trim() ).filter( Boolean ) : []
		);

		/* ---- AJAX helpers ---- */
		const doSearch = () => {
			if ( ! searchKeyword.trim() ) return;
			setIsSearching( true );
			const fd = new FormData();
			fd.append( 'action', 'azon_mate_search_products' );
			fd.append( 'nonce', window.azonMateBlock?.nonce || '' );
			fd.append( 'keywords', searchKeyword );
			fetch( window.azonMateBlock?.ajaxUrl || '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: fd,
			} )
				.then( ( r ) => r.json() )
				.then( ( data ) => {
					setIsSearching( false );
					if ( data.success && data.data.products ) {
						setSearchResults( data.data.products );
					}
				} )
				.catch( () => setIsSearching( false ) );
		};

		const browseMyProducts = () => {
			setIsSearching( true );
			const fd = new FormData();
			fd.append( 'action', 'azon_mate_get_manual_products' );
			fd.append( 'nonce', window.azonMateBlock?.nonce || '' );
			fd.append( 'search', searchKeyword );
			fetch( window.azonMateBlock?.ajaxUrl || '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: fd,
			} )
				.then( ( r ) => r.json() )
				.then( ( data ) => {
					setIsSearching( false );
					if ( data.success && data.data.products ) {
						setSearchResults( data.data.products );
					}
				} )
				.catch( () => setIsSearching( false ) );
		};

		const addProduct = ( asin ) => {
			if ( selectedProducts.includes( asin ) ) return;
			const updated = [ ...selectedProducts, asin ];
			setSelectedProducts( updated );
			setAttributes( { asins: updated.join( ',' ) } );
		};

		const removeProduct = ( asin ) => {
			const updated = selectedProducts.filter( ( a ) => a !== asin );
			setSelectedProducts( updated );
			setAttributes( { asins: updated.join( ',' ) } );
		};

		/* ============================================================
		   STEP 1 – layout picker (no layout selected yet)
		   ============================================================ */
		if ( ! layout ) {
			return (
				<div { ...blockProps }>
					<Placeholder
						icon={ showcaseIcon }
						label={ __( 'AzonMate Showcase', 'azonmate' ) }
						instructions={ __( 'Choose a pre-built layout — products will render exactly as designed.', 'azonmate' ) }
					>
						<div className="azonmate-showcase-picker">
							{ LAYOUTS.map( ( l ) => (
								<button
									key={ l.value }
									type="button"
									className="azonmate-showcase-picker__card"
									onClick={ () => setAttributes( { layout: l.value } ) }
								>
									<span className="azonmate-showcase-picker__icon">{ l.icon }</span>
									<strong className="azonmate-showcase-picker__label">{ l.label }</strong>
									<span className="azonmate-showcase-picker__desc">{ l.desc }</span>
									<span className="azonmate-showcase-picker__badge">
										{ l.type === 'multi' ? __( 'Multi-product', 'azonmate' ) : __( 'Single-product', 'azonmate' ) }
									</span>
								</button>
							) ) }
						</div>
					</Placeholder>
				</div>
			);
		}

		/* ============================================================
		   STEP 2 – product search (layout chosen, no products yet)
		   ============================================================ */
		const chosenLayout = LAYOUTS.find( ( l ) => l.value === layout ) || LAYOUTS[ 0 ];
		const isSingle = chosenLayout.type === 'single';

		if ( ! asins ) {
			return (
				<div { ...blockProps }>
					<Placeholder
						icon={ showcaseIcon }
						label={ chosenLayout.label }
						instructions={
							isSingle
								? __( 'Search and select one product for this layout.', 'azonmate' )
								: __( 'Search and add products for this layout.', 'azonmate' )
						}
					>
						<div className="azonmate-block-search">
							<div className="azonmate-block-search__input-row">
								<TextControl
									placeholder={ __( 'Search products…', 'azonmate' ) }
									value={ searchKeyword }
									onChange={ setSearchKeyword }
									onKeyDown={ ( e ) => e.key === 'Enter' && doSearch() }
								/>
								<Button variant="primary" onClick={ doSearch } disabled={ isSearching }>
									{ isSearching ? <Spinner /> : __( 'Search', 'azonmate' ) }
								</Button>
								<Button variant="secondary" onClick={ browseMyProducts } disabled={ isSearching }>
									{ __( 'My Products', 'azonmate' ) }
								</Button>
							</div>

							<div className="azonmate-block-search__input-row">
								<TextControl
									placeholder={
										isSingle
											? __( 'Or paste ASIN directly…', 'azonmate' )
											: __( 'Or paste ASINs: B08N5WRWNW, B09V3KXJPB', 'azonmate' )
									}
									onChange={ ( val ) => {
										if ( val.trim() ) {
											if ( isSingle && /^[A-Z0-9]{10}$/i.test( val.trim() ) ) {
												setAttributes( { asins: val.trim().toUpperCase() } );
											} else if ( ! isSingle ) {
												setAttributes( { asins: val.trim() } );
											}
										}
									} }
								/>
							</div>

							{ /* selected products tags (multi-product) */ }
							{ selectedProducts.length > 0 && ! isSingle && (
								<div className="azonmate-block-search__selected">
									<strong>{ __( 'Selected:', 'azonmate' ) }</strong>
									{ selectedProducts.map( ( a ) => (
										<span key={ a } className="azonmate-block-search__tag">
											{ a }
											<button type="button" onClick={ () => removeProduct( a ) }>&times;</button>
										</span>
									) ) }
									<Button
										variant="primary"
										isSmall
										onClick={ () => setAttributes( { asins: selectedProducts.join( ',' ) } ) }
									>
										{ __( 'Done', 'azonmate' ) }
									</Button>
								</div>
							) }

							{ searchResults.length > 0 && (
								<div className="azonmate-block-search__results">
									{ searchResults.map( ( product ) => (
										<div
											key={ product.asin }
											className="azonmate-block-search__result"
											onClick={ () =>
												isSingle
													? setAttributes( { asins: product.asin } )
													: addProduct( product.asin )
											}
											role="button"
											tabIndex={ 0 }
											onKeyDown={ ( e ) =>
												e.key === 'Enter' &&
												( isSingle
													? setAttributes( { asins: product.asin } )
													: addProduct( product.asin ) )
											}
										>
											{ ( product.image_medium || product.image_small || product.image ) && (
												<img src={ product.image_medium || product.image_small || product.image } alt="" />
											) }
											<span className="azonmate-block-search__result-title">{ product.title }</span>
											{ ( product.price_display || product.price ) && (
												<span className="azonmate-block-search__result-price">
													{ product.price_display || product.price }
												</span>
											) }
										</div>
									) ) }
								</div>
							) }

							<div style={ { marginTop: '12px' } }>
								<Button variant="tertiary" isSmall onClick={ () => setAttributes( { layout: '' } ) }>
									{ __( '← Change Layout', 'azonmate' ) }
								</Button>
							</div>
						</div>
					</Placeholder>
				</div>
			);
		}

		/* ============================================================
		   STEP 3 – live preview (layout + products selected)
		   ============================================================ */
		return (
			<div { ...blockProps }>
				<InspectorControls>
					<PanelBody title={ __( 'Showcase Layout', 'azonmate' ) }>
						<SelectControl
							label={ __( 'Layout', 'azonmate' ) }
							value={ layout }
							options={ LAYOUTS.map( ( l ) => ( { label: l.label, value: l.value } ) ) }
							onChange={ ( val ) => setAttributes( { layout: val } ) }
						/>
						<RangeControl
							label={ __( 'Columns', 'azonmate' ) }
							value={ columns }
							onChange={ ( val ) => setAttributes( { columns: val } ) }
							min={ 1 }
							max={ 6 }
						/>
						<RangeControl
							label={ __( 'Max Products', 'azonmate' ) }
							value={ max }
							onChange={ ( val ) => setAttributes( { max: val } ) }
							min={ 1 }
							max={ 20 }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Display Options', 'azonmate' ) } initialOpen={ false }>
						<TextControl
							label={ __( 'Heading', 'azonmate' ) }
							value={ heading }
							onChange={ ( val ) => setAttributes( { heading: val } ) }
						/>
						<ToggleControl
							label={ __( 'Show Badge', 'azonmate' ) }
							checked={ showBadge }
							onChange={ ( val ) => setAttributes( { showBadge: val } ) }
						/>
						<ToggleControl
							label={ __( 'Show Price', 'azonmate' ) }
							checked={ showPrice }
							onChange={ ( val ) => setAttributes( { showPrice: val } ) }
						/>
						<ToggleControl
							label={ __( 'Show Rating', 'azonmate' ) }
							checked={ showRating }
							onChange={ ( val ) => setAttributes( { showRating: val } ) }
						/>
						<TextControl
							label={ __( 'Button Text', 'azonmate' ) }
							value={ buttonText }
							onChange={ ( val ) => setAttributes( { buttonText: val } ) }
							help={ __( 'Leave empty for default.', 'azonmate' ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Products', 'azonmate' ) } initialOpen={ false }>
						<TextControl
							label={ __( 'ASINs (comma-separated)', 'azonmate' ) }
							value={ asins }
							onChange={ ( val ) => setAttributes( { asins: val } ) }
						/>
						<Button
							variant="secondary"
							isSmall
							onClick={ () => setAttributes( { asins: '' } ) }
							style={ { marginBottom: '10px' } }
						>
							{ __( 'Replace Products', 'azonmate' ) }
						</Button>
						<Button
							variant="tertiary"
							isSmall
							onClick={ () => setAttributes( { layout: '', asins: '' } ) }
						>
							{ __( 'Change Layout', 'azonmate' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div className="azonmate-editor-preview">
					<div className="azonmate-editor-preview__toolbar">
						<span style={ { fontWeight: 600, color: '#ff9900' } }>
							{ chosenLayout.icon } { chosenLayout.label }
						</span>
						<Button variant="tertiary" isSmall onClick={ () => setAttributes( { asins: '' } ) }>
							{ __( 'Replace', 'azonmate' ) }
						</Button>
						<Button variant="tertiary" isSmall onClick={ () => setAttributes( { layout: '', asins: '' } ) }>
							{ __( 'Change Layout', 'azonmate' ) }
						</Button>
					</div>
					<ServerSideRender
						block="azonmate/showcase"
						attributes={ attributes }
					/>
				</div>
			</div>
		);
	},
	save: () => null,
} );
