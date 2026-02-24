/**
 * AzonMate Collage – Block Entry Point
 *
 * Step 1: Search / add products.
 * Step 2: Live server-side preview with auto-layout.
 *
 * @package AzonMate
 * @since   1.6.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	ToggleControl,
	Placeholder,
	Button,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState } from '@wordpress/element';

/* ------------------------------------------------------------------ */
/*  Brand icon – orange collage grid                                  */
/* ------------------------------------------------------------------ */
const collageIcon = wp.element.createElement(
	'svg',
	{ width: 24, height: 24, viewBox: '0 0 24 24', xmlns: 'http://www.w3.org/2000/svg' },
	wp.element.createElement( 'rect', { x: 1, y: 1, width: 22, height: 22, rx: 4, fill: '#ff9900' } ),
	wp.element.createElement( 'rect', { x: 3, y: 3, width: 10, height: 8, rx: 1.5, fill: '#fff' } ),
	wp.element.createElement( 'rect', { x: 15, y: 3, width: 6, height: 8, rx: 1.5, fill: '#fff' } ),
	wp.element.createElement( 'rect', { x: 3, y: 13, width: 6, height: 8, rx: 1.5, fill: '#fff' } ),
	wp.element.createElement( 'rect', { x: 11, y: 13, width: 10, height: 8, rx: 1.5, fill: '#fff' } )
);

registerBlockType( 'azonmate/collage', {
	icon: collageIcon,
	edit: function Edit( { attributes, setAttributes } ) {
		const { asins, max, heading, showBadge, showPrice, showRating, buttonText, gap } = attributes;
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
		   STEP 1 – product search (no products yet)
		   ============================================================ */
		if ( ! asins ) {
			return (
				<div { ...blockProps }>
					<Placeholder
						icon={ collageIcon }
						label={ __( 'AzonMate Collage', 'azonmate' ) }
						instructions={ __( 'Search and add 2–12 products for a dynamic collage layout.', 'azonmate' ) }
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
									placeholder={ __( 'Or paste ASINs: B08N5WRWNW, B09V3KXJPB', 'azonmate' ) }
									onChange={ ( val ) => {
										if ( val.trim() ) {
											setAttributes( { asins: val.trim() } );
										}
									} }
								/>
							</div>

							{ selectedProducts.length > 0 && (
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
											onClick={ () => addProduct( product.asin ) }
											role="button"
											tabIndex={ 0 }
											onKeyDown={ ( e ) =>
												e.key === 'Enter' && addProduct( product.asin )
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
						</div>
					</Placeholder>
				</div>
			);
		}

		/* ============================================================
		   STEP 2 – live preview (products selected)
		   ============================================================ */
		return (
			<div { ...blockProps }>
				<InspectorControls>
					<PanelBody title={ __( 'Collage Settings', 'azonmate' ) }>
						<RangeControl
							label={ __( 'Max Products', 'azonmate' ) }
							value={ max }
							onChange={ ( val ) => setAttributes( { max: val } ) }
							min={ 1 }
							max={ 20 }
						/>
						<RangeControl
							label={ __( 'Gap (px)', 'azonmate' ) }
							value={ gap }
							onChange={ ( val ) => setAttributes( { gap: val } ) }
							min={ 0 }
							max={ 40 }
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
					</PanelBody>
				</InspectorControls>

				<div className="azonmate-editor-preview">
					<div className="azonmate-editor-preview__toolbar">
						<span style={ { fontWeight: 600, color: '#ff9900' } }>
							🖼 { __( 'Collage', 'azonmate' ) } — { asins.split( ',' ).filter( Boolean ).length } { __( 'products', 'azonmate' ) }
						</span>
						<Button variant="tertiary" isSmall onClick={ () => setAttributes( { asins: '' } ) }>
							{ __( 'Replace', 'azonmate' ) }
						</Button>
					</div>
					<ServerSideRender
						block="azonmate/collage"
						attributes={ attributes }
					/>
				</div>
			</div>
		);
	},
	save: () => null,
} );
