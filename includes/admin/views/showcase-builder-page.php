<?php
/**
 * Showcase Builder – Visual shortcode generator (WYSIWYG preview).
 *
 * Preview uses the *exact same* CSS classes as the frontend templates so
 * the output is 1:1 with what appears on live posts.
 *
 * @package AzonMate\Admin\Views
 * @since   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// $products is passed from ShowcaseBuilder::render_page().
$product_list = array();
foreach ( $products as $p ) {
	$product_list[] = $p->to_array();
}

if ( ! function_exists( 'azonmate_render_admin_header' ) ) {
	require_once __DIR__ . '/partials/admin-bar.php';
}
?>

<div class="wrap azonmate-showcase-builder">
	<?php azonmate_render_admin_header(); ?>
	<h1>
		<span class="dashicons dashicons-layout" style="margin-right:8px;"></span>
		<?php esc_html_e( 'Showcase Builder', 'azonmate' ); ?>
	</h1>
	<p class="description"><?php esc_html_e( 'Pick a design, select your products, copy the shortcode — done.', 'azonmate' ); ?></p>

	<!-- ================================================================
	     STEP 1 – Pick a Design
	     ================================================================ -->
	<div class="azm-sb-section">
		<h2 class="azm-sb-step"><span class="azm-sb-step-num">1</span> <?php esc_html_e( 'Pick a Design', 'azonmate' ); ?></h2>

		<h3 class="azm-sb-group-label"><?php esc_html_e( 'Multi-Product Layouts', 'azonmate' ); ?></h3>
		<div class="azm-sb-layouts azm-sb-layouts--multi">
			<label class="azm-sb-layout-card azm-sb-layout-card--selected" data-layout="grid">
				<input type="radio" name="azm_layout" value="grid" checked hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--grid"><span></span><span></span><span></span><span></span><span></span><span></span></div>
				<strong><?php esc_html_e( 'Grid Cards', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( 'Responsive card grid with badges & CTA', 'azonmate' ); ?></small>
			</label>
			<label class="azm-sb-layout-card" data-layout="list">
				<input type="radio" name="azm_layout" value="list" hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--list"><span></span><span></span><span></span></div>
				<strong><?php esc_html_e( 'Row List', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( 'Horizontal rows – image, details, price', 'azonmate' ); ?></small>
			</label>
			<label class="azm-sb-layout-card" data-layout="masonry">
				<input type="radio" name="azm_layout" value="masonry" hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--masonry"><span class="tall"></span><span></span><span></span><span class="tall"></span></div>
				<strong><?php esc_html_e( 'Masonry', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( 'Pinterest-style staggered collage', 'azonmate' ); ?></small>
			</label>
			<label class="azm-sb-layout-card" data-layout="table">
				<input type="radio" name="azm_layout" value="table" hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--table"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
				<strong><?php esc_html_e( 'Comparison Table', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( 'Side-by-side comparison columns', 'azonmate' ); ?></small>
			</label>
		</div>

		<h3 class="azm-sb-group-label"><?php esc_html_e( 'Single-Product Layouts', 'azonmate' ); ?></h3>
		<div class="azm-sb-layouts azm-sb-layouts--single">
			<label class="azm-sb-layout-card" data-layout="hero">
				<input type="radio" name="azm_layout" value="hero" hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--hero"><span class="img"></span><span class="body"></span></div>
				<strong><?php esc_html_e( 'Hero Card', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( 'Featured large product spotlight', 'azonmate' ); ?></small>
			</label>
			<label class="azm-sb-layout-card" data-layout="compact">
				<input type="radio" name="azm_layout" value="compact" hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--compact"><span class="thumb"></span><span class="text"></span><span class="btn"></span></div>
				<strong><?php esc_html_e( 'Compact', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( 'Slim inline card for mid-article', 'azonmate' ); ?></small>
			</label>
			<label class="azm-sb-layout-card" data-layout="split">
				<input type="radio" name="azm_layout" value="split" hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--split"><span class="left"></span><span class="right"></span></div>
				<strong><?php esc_html_e( 'Split', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( '50/50 image + details panels', 'azonmate' ); ?></small>
			</label>
			<label class="azm-sb-layout-card" data-layout="deal">
				<input type="radio" name="azm_layout" value="deal" hidden />
				<div class="azm-sb-layout-preview azm-sb-layout-preview--deal"><span class="accent"></span><span class="img"></span><span class="body"></span></div>
				<strong><?php esc_html_e( 'Deal Card', 'azonmate' ); ?></strong>
				<small><?php esc_html_e( 'Price-drop focus with savings', 'azonmate' ); ?></small>
			</label>
		</div>
	</div>

	<!-- ================================================================
	     STEP 2 – Select Products
	     ================================================================ -->
	<div class="azm-sb-section">
		<h2 class="azm-sb-step"><span class="azm-sb-step-num">2</span> <?php esc_html_e( 'Select Products', 'azonmate' ); ?></h2>

		<?php if ( empty( $product_list ) ) : ?>
			<div class="azm-sb-empty">
				<span class="dashicons dashicons-warning"></span>
				<p><?php esc_html_e( 'No products found. Add products first in', 'azonmate' ); ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=azonmate-products' ) ); ?>"><?php esc_html_e( 'AzonMate > Products', 'azonmate' ); ?></a>.
				</p>
			</div>
		<?php else : ?>
			<p class="description" id="azm-product-hint"><?php esc_html_e( 'Select 2–10 products for multi-product layouts, or 1 product for single-product layouts:', 'azonmate' ); ?></p>
			<div class="azm-sb-product-picker">
				<?php foreach ( $product_list as $p ) :
					$img = ! empty( $p['image_medium'] ) ? $p['image_medium'] : ( ! empty( $p['image_small'] ) ? $p['image_small'] : '' );
					?>
					<label class="azm-sb-product-item" data-asin="<?php echo esc_attr( $p['asin'] ); ?>">
						<input type="checkbox" name="azm_products[]" value="<?php echo esc_attr( $p['asin'] ); ?>" hidden />
						<div class="azm-sb-product-thumb">
							<?php if ( $img ) : ?>
								<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>" />
							<?php else : ?>
								<span class="dashicons dashicons-format-image"></span>
							<?php endif; ?>
						</div>
						<div class="azm-sb-product-info">
							<strong><?php echo esc_html( wp_trim_words( $p['title'], 10 ) ); ?></strong>
							<span class="azm-sb-product-meta">
								<?php echo esc_html( $p['asin'] ); ?>
								<?php if ( ! empty( $p['price_display'] ) ) : ?>
									&middot; <?php echo esc_html( $p['price_display'] ); ?>
								<?php endif; ?>
								<?php if ( ! empty( $p['badge_label'] ) ) : ?>
									<span class="azm-sb-product-badge"><?php echo esc_html( $p['badge_label'] ); ?></span>
								<?php endif; ?>
							</span>
						</div>
						<span class="azm-sb-check"><span class="dashicons dashicons-yes-alt"></span></span>
					</label>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<!-- ================================================================
	     STEP 3 – Optional Extras
	     ================================================================ -->
	<div class="azm-sb-section azm-sb-section--extras">
		<h2 class="azm-sb-step"><span class="azm-sb-step-num">3</span> <?php esc_html_e( 'Optional Extras', 'azonmate' ); ?></h2>
		<div class="azm-sb-extras-grid">
			<div class="azm-sb-field">
				<label for="azm-heading"><?php esc_html_e( 'Section Heading', 'azonmate' ); ?></label>
				<input type="text" id="azm-heading" placeholder="<?php esc_attr_e( 'e.g., Top Picks for 2026', 'azonmate' ); ?>" />
			</div>
			<div class="azm-sb-field">
				<label for="azm-btn-text"><?php esc_html_e( 'Button Text', 'azonmate' ); ?></label>
				<input type="text" id="azm-btn-text" placeholder="<?php esc_attr_e( 'e.g., Check Price, Get Deal', 'azonmate' ); ?>" />
			</div>
			<div class="azm-sb-field">
				<label for="azm-columns"><?php esc_html_e( 'Columns (Grid/Masonry)', 'azonmate' ); ?></label>
				<select id="azm-columns">
					<option value=""><?php esc_html_e( 'Auto (3)', 'azonmate' ); ?></option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
				</select>
			</div>
		</div>
	</div>

	<!-- ================================================================
	     STEP 4 – Copy Your Shortcode
	     ================================================================ -->
	<div class="azm-sb-section azm-sb-section--output" id="azm-sb-output-section" style="display:none;">
		<h2 class="azm-sb-step"><span class="azm-sb-step-num">4</span> <?php esc_html_e( 'Copy Your Shortcode', 'azonmate' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Paste this shortcode into any post or page:', 'azonmate' ); ?></p>
		<div class="azm-sb-shortcode-box">
			<code id="azm-sb-shortcode"></code>
			<button type="button" id="azm-sb-copy" class="button button-primary">
				<span class="dashicons dashicons-clipboard" style="margin-top:3px;"></span>
				<?php esc_html_e( 'Copy Shortcode', 'azonmate' ); ?>
			</button>
		</div>
		<p id="azm-sb-copied" class="azm-sb-copied" style="display:none;">&#10003; <?php esc_html_e( 'Copied!', 'azonmate' ); ?></p>

		<!-- WYSIWYG Preview – uses the real frontend azonmate-showcase classes loaded from azonmate-public.css + azonmate-showcase.css -->
		<div class="azm-sb-preview-section">
			<h3><?php esc_html_e( 'Live Preview (exactly matches post output)', 'azonmate' ); ?></h3>
			<div class="azm-sb-preview" id="azm-sb-preview"></div>
		</div>
	</div>
	<?php azonmate_render_admin_footer(); ?>
</div>

<!-- ====================================================================
     Builder UI Styles (admin-only, separate from frontend showcase CSS)
     ==================================================================== -->
<style>
.azonmate-showcase-builder { max-width: 1100px; }
.azm-sb-section { background:#fff; border:1px solid #e0e0e0; border-radius:10px; padding:24px 28px; margin-bottom:20px; }
.azm-sb-step { display:flex; align-items:center; gap:10px; font-size:16px; margin:0 0 16px; }
.azm-sb-step-num { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; background:#ff9900; color:#000; font-weight:800; font-size:15px; border-radius:50%; }
.azm-sb-group-label { font-size:13px; font-weight:600; color:#555; text-transform:uppercase; letter-spacing:.5px; margin:18px 0 10px; }
.azm-sb-group-label:first-of-type { margin-top:0; }

/* Layout Cards */
.azm-sb-layouts { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
@media (max-width:900px) { .azm-sb-layouts { grid-template-columns:repeat(2,1fr); } }
@media (max-width:500px) { .azm-sb-layouts { grid-template-columns:1fr; } }
.azm-sb-layout-card { display:flex; flex-direction:column; align-items:center; text-align:center; padding:16px 12px; border:2px solid #e0e0e0; border-radius:10px; cursor:pointer; transition:border-color .2s, box-shadow .2s, transform .15s; background:#fafafa; }
.azm-sb-layout-card:hover { border-color:#ccc; box-shadow:0 4px 14px rgba(0,0,0,.07); transform:translateY(-2px); }
.azm-sb-layout-card--selected { border-color:#ff9900 !important; background:#fff8ee; box-shadow:0 0 0 3px rgba(255,153,0,.18); }
.azm-sb-layout-card strong { margin-top:8px; font-size:13px; }
.azm-sb-layout-card small { color:#666; font-size:11px; margin-top:3px; }

/* Mini previews */
.azm-sb-layout-preview { width:100%; height:72px; display:flex; gap:4px; flex-wrap:wrap; justify-content:center; align-items:center; }
.azm-sb-layout-preview span { background:#ddd; border-radius:3px; transition:background .2s; }
.azm-sb-layout-card--selected .azm-sb-layout-preview span { background:#ffcc80; }
.azm-sb-layout-preview--grid span { width:30%; height:30px; }
.azm-sb-layout-preview--list span { width:92%; height:20px; }
.azm-sb-layout-preview--masonry { align-items:flex-start; }
.azm-sb-layout-preview--masonry span { width:44%; height:28px; }
.azm-sb-layout-preview--masonry span.tall { height:46px; }
.azm-sb-layout-preview--table span { width:28%; height:20px; border-radius:2px; }
.azm-sb-layout-preview--hero { gap:6px; }
.azm-sb-layout-preview--hero .img { width:40%; height:60px; }
.azm-sb-layout-preview--hero .body { width:55%; height:60px; }
.azm-sb-layout-preview--compact { gap:6px; }
.azm-sb-layout-preview--compact .thumb { width:20%; height:40px; border-radius:4px; }
.azm-sb-layout-preview--compact .text { width:50%; height:40px; }
.azm-sb-layout-preview--compact .btn { width:22%; height:40px; border-radius:5px; }
.azm-sb-layout-preview--split .left { width:48%; height:58px; }
.azm-sb-layout-preview--split .right { width:48%; height:58px; }
.azm-sb-layout-preview--deal { gap:5px; position:relative; }
.azm-sb-layout-preview--deal .accent { width:4px; height:52px; border-radius:2px; background:#ff9900 !important; }
.azm-sb-layout-preview--deal .img { width:28%; height:52px; }
.azm-sb-layout-preview--deal .body { width:62%; height:52px; }

/* Product Picker */
.azm-sb-product-picker { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:10px; max-height:420px; overflow-y:auto; padding:4px; }
.azm-sb-product-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border:2px solid #eee; border-radius:8px; cursor:pointer; transition:border-color .15s, background .15s; position:relative; }
.azm-sb-product-item:hover { background:#fafafa; border-color:#ccc; }
.azm-sb-product-item--selected { border-color:#ff9900 !important; background:#fffcf5 !important; }
.azm-sb-product-thumb { flex:0 0 50px; height:50px; border-radius:6px; overflow:hidden; background:#f5f5f5; display:flex; align-items:center; justify-content:center; }
.azm-sb-product-thumb img { max-width:100%; max-height:100%; object-fit:contain; }
.azm-sb-product-thumb .dashicons { font-size:24px; color:#bbb; }
.azm-sb-product-info { flex:1; min-width:0; }
.azm-sb-product-info strong { display:block; font-size:13px; line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.azm-sb-product-meta { font-size:11px; color:#888; margin-top:2px; }
.azm-sb-product-badge { background:linear-gradient(135deg,#ff9900,#e68a00); color:#000; font-size:9px; font-weight:700; text-transform:uppercase; padding:1px 5px; border-radius:3px; margin-left:4px; }
.azm-sb-check { flex:0 0 24px; opacity:0; color:#ff9900; transition:opacity .15s; }
.azm-sb-product-item--selected .azm-sb-check { opacity:1; }
.azm-sb-check .dashicons { font-size:22px; }

/* Extras */
.azm-sb-extras-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
@media (max-width:700px) { .azm-sb-extras-grid { grid-template-columns:1fr; } }
.azm-sb-field label { display:block; font-weight:600; font-size:13px; margin-bottom:4px; }
.azm-sb-field input, .azm-sb-field select { width:100%; }

/* Output */
.azm-sb-section--output { border-color:#ff9900; background:#fffcf5; }
.azm-sb-shortcode-box { display:flex; align-items:center; gap:12px; background:#fff; border:1px solid #ddd; border-radius:8px; padding:12px 16px; margin-top:10px; }
.azm-sb-shortcode-box code { flex:1; font-size:13px; background:transparent; word-break:break-all; color:#333; user-select:all; }
.azm-sb-copied { color:#00a32a; font-weight:600; margin-top:8px; font-size:13px; }

/* Preview */
.azm-sb-preview-section { margin-top:24px; }
.azm-sb-preview-section h3 { margin:0 0 12px; font-size:14px; }
.azm-sb-preview { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px; min-height:100px; overflow:hidden; }

/* Empty */
.azm-sb-empty { text-align:center; padding:30px; color:#888; }
.azm-sb-empty .dashicons { font-size:32px; width:32px; height:32px; margin-bottom:8px; color:#ccc; }
</style>

<!-- ====================================================================
     Script – generates shortcode + builds WYSIWYG preview using real
     azonmate-showcase classes (CSS loaded from the actual stylesheets).
     ==================================================================== -->
<script>
(function() {
	'use strict';

	var allProducts  = <?php echo wp_json_encode( $product_list ); ?>;
	var selectedAsins = [];
	var selectedLayout = 'grid';
	var singleLayouts = ['hero','compact','split','deal'];

	// --- Helpers ---
	function esc(str) { if (!str) return ''; var d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
	function getProduct(asin) { for (var i = 0; i < allProducts.length; i++) { if (allProducts[i].asin === asin) return allProducts[i]; } return null; }
	function stars(r) {
		if (!r) return '';
		var full = Math.floor(r), half = (r - full >= 0.5) ? 1 : 0, empty = 5 - full - half, s = '';
		for (var i = 0; i < full; i++) s += '\u2605';
		if (half) s += '\u2605';
		for (var j = 0; j < empty; j++) s += '\u2606';
		return '<span class="azonmate-showcase__rating" style="margin-bottom:0;display:inline-flex;gap:0;color:#ffa41c;font-size:14px;">' + s + '</span>';
	}
	function isSingle() { return singleLayouts.indexOf(selectedLayout) !== -1; }

	// --- Layout selection ---
	document.querySelectorAll('.azm-sb-layout-card').forEach(function(card) {
		card.addEventListener('click', function() {
			document.querySelectorAll('.azm-sb-layout-card').forEach(function(c) { c.classList.remove('azm-sb-layout-card--selected'); });
			card.classList.add('azm-sb-layout-card--selected');
			card.querySelector('input').checked = true;
			selectedLayout = card.dataset.layout;
			updateHint();
			updateOutput();
		});
	});

	function updateHint() {
		var hint = document.getElementById('azm-product-hint');
		if (!hint) return;
		if (isSingle()) {
			hint.textContent = 'Select 1 product for this single-product layout:';
		} else {
			hint.textContent = 'Select 2\u201310 products for multi-product layouts:';
		}
	}

	// --- Product selection ---
	document.querySelectorAll('.azm-sb-product-item').forEach(function(item) {
		item.addEventListener('click', function() {
			var asin = item.dataset.asin;
			var idx = selectedAsins.indexOf(asin);
			if (idx === -1) {
				selectedAsins.push(asin);
				item.classList.add('azm-sb-product-item--selected');
				item.querySelector('input').checked = true;
			} else {
				selectedAsins.splice(idx, 1);
				item.classList.remove('azm-sb-product-item--selected');
				item.querySelector('input').checked = false;
			}
			updateOutput();
		});
	});

	// --- Extras ---
	var headingEl = document.getElementById('azm-heading');
	var btnTextEl = document.getElementById('azm-btn-text');
	var columnsEl = document.getElementById('azm-columns');
	if (headingEl) headingEl.addEventListener('input', updateOutput);
	if (btnTextEl) btnTextEl.addEventListener('input', updateOutput);
	if (columnsEl) columnsEl.addEventListener('change', updateOutput);

	// --- Copy ---
	var copyBtn = document.getElementById('azm-sb-copy');
	if (copyBtn) {
		copyBtn.addEventListener('click', function() {
			var code = document.getElementById('azm-sb-shortcode').textContent;
			if (navigator.clipboard) { navigator.clipboard.writeText(code); }
			else { var ta = document.createElement('textarea'); ta.value = code; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); }
			var msg = document.getElementById('azm-sb-copied');
			msg.style.display = 'block';
			setTimeout(function() { msg.style.display = 'none'; }, 2000);
		});
	}

	function updateOutput() {
		var outputSection = document.getElementById('azm-sb-output-section');
		if (selectedAsins.length === 0) { outputSection.style.display = 'none'; return; }
		outputSection.style.display = '';

		var heading = headingEl ? headingEl.value.trim() : '';
		var btnText = btnTextEl ? btnTextEl.value.trim() : '';
		var columns = columnsEl ? columnsEl.value : '';

		var sc = '[azonmate showcase="' + selectedAsins.join(',') + '" layout="' + selectedLayout + '"';
		if (columns && (selectedLayout === 'grid' || selectedLayout === 'masonry')) sc += ' columns="' + columns + '"';
		if (heading) sc += ' heading="' + heading + '"';
		if (btnText) sc += ' button_text="' + btnText + '"';
		sc += ']';

		document.getElementById('azm-sb-shortcode').textContent = sc;
		renderPreview();
	}

	// =========================================================
	// WYSIWYG Preview – uses REAL azonmate-showcase__* classes
	// so the preview looks identical to the live post.
	// The actual CSS is loaded from azonmate-public.css and
	// azonmate-showcase.css by ShowcaseBuilder::enqueue_preview_assets().
	// =========================================================
	function renderPreview() {
		var container = document.getElementById('azm-sb-preview');
		var prods = selectedAsins.map(getProduct).filter(Boolean);
		if (!prods.length) { container.innerHTML = ''; return; }

		var heading = headingEl ? headingEl.value.trim() : '';
		var btnText = btnTextEl ? btnTextEl.value.trim() : '';
		if (!btnText) btnText = 'Buy on Amazon';
		var columns = columnsEl ? columnsEl.value : '';

		var html = '';

		// Route to builder function by layout
		switch (selectedLayout) {
			case 'grid':    html = buildGrid(prods, heading, btnText, columns); break;
			case 'list':    html = buildList(prods, heading, btnText); break;
			case 'masonry': html = buildMasonry(prods, heading, btnText, columns); break;
			case 'table':   html = buildTable(prods, heading, btnText); break;
			case 'hero':    html = buildHero(prods[0], heading, btnText); break;
			case 'compact': html = buildCompact(prods[0], heading, btnText); break;
			case 'split':   html = buildSplit(prods[0], heading, btnText); break;
			case 'deal':    html = buildDeal(prods[0], heading, btnText); break;
		}

		container.innerHTML = html;
	}

	// --- Heading helper ---
	function headingHtml(heading) {
		if (!heading) return '';
		return '<h2 class="azonmate-showcase__heading">' + esc(heading) + '</h2>';
	}

	// --- Image helper ---
	function img(p, size) {
		var src = (size === 'small') ? (p.image_small || p.image_medium || '') : (p.image_large || p.image_medium || p.image_small || '');
		if (!src) return '';
		return '<img src="' + esc(src) + '" alt="' + esc(p.title) + '" class="azonmate-showcase__image" loading="lazy" />';
	}

	// --- Badge/savings helpers ---
	function badgeHtml(p, extraStyle) {
		if (!p.badge_label) return '';
		return '<span class="azonmate-showcase__badge"' + (extraStyle ? ' style="' + extraStyle + '"' : '') + '>' + esc(p.badge_label) + '</span>';
	}
	function savingsHtml(p, cls) {
		if (!p.savings_percentage) return '';
		return '<span class="azonmate-showcase__savings' + (cls ? ' ' + cls : '') + '">-' + p.savings_percentage + '%</span>';
	}
	function brandHtml(p) { if (!p.brand) return ''; return '<span class="azonmate-showcase__brand">' + esc(p.brand) + '</span>'; }
	function titleHtml(p, words, cls) {
		var t = p.title || '';
		if (words) t = t.split(' ').slice(0, words).join(' ');
		return '<h3 class="azonmate-showcase__title' + (cls ? ' ' + cls : '') + '"><a href="#">' + esc(t) + '</a></h3>';
	}
	function ratingHtml(p) {
		if (!p.rating) return '';
		var h = '<div class="azonmate-showcase__rating">' + stars(p.rating);
		if (p.review_count) h += ' <span class="azonmate-showcase__reviews">(' + Number(p.review_count).toLocaleString() + ')</span>';
		return h + '</div>';
	}
	function priceHtml(p, cls) {
		if (!p.price_display) return '';
		var h = '<div class="azonmate-showcase__price-wrap' + (cls ? ' ' + cls : '') + '">';
		h += '<span class="azonmate-showcase__price' + (cls && cls.indexOf('vertical') !== -1 ? ' azonmate-showcase__price--large' : '') + '">' + esc(p.price_display) + '</span>';
		if (p.list_price_display && p.list_price_display !== p.price_display) {
			h += ' <span class="azonmate-showcase__old-price"><del>' + esc(p.list_price_display) + '</del></span>';
		}
		h += '</div>';
		return h;
	}
	function btnHtml(btn) {
		return '<div class="azonmate-showcase__action"><a href="#" class="azonmate-buy-btn" onclick="return false;">' + esc(btn) + '</a></div>';
	}
	function descHtml(p, words) {
		if (!p.description) return '';
		var t = words ? p.description.split(' ').slice(0, words).join(' ') : p.description;
		return '<p class="azonmate-showcase__desc">' + esc(t) + '</p>';
	}
	function featuresHtml(p, max) {
		if (!p.features || !p.features.length) return '';
		var items = p.features.slice(0, max || 3);
		var h = '<ul class="azonmate-showcase__features">';
		items.forEach(function(f) { h += '<li>' + esc(f) + '</li>'; });
		return h + '</ul>';
	}

	// =========================================================
	// Grid
	// =========================================================
	function buildGrid(prods, heading, btn, cols) {
		var colsClass = cols ? ' azonmate-showcase-grid--cols-' + cols : '';
		var h = '<div class="azonmate-showcase azonmate-showcase--grid' + colsClass + '">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__grid">';
		prods.forEach(function(p) {
			h += '<div class="azonmate-showcase__card">';
			h += badgeHtml(p);
			h += savingsHtml(p);
			h += '<div class="azonmate-showcase__image-wrap">' + img(p, 'large') + '</div>';
			h += '<div class="azonmate-showcase__content">';
			h += brandHtml(p) + titleHtml(p, 14) + ratingHtml(p) + priceHtml(p) + descHtml(p, 18);
			h += '</div>';
			h += btnHtml(btn);
			h += '</div>';
		});
		h += '</div></div>';
		return h;
	}

	// =========================================================
	// List
	// =========================================================
	function buildList(prods, heading, btn) {
		var h = '<div class="azonmate-showcase azonmate-showcase--list">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__rows">';
		prods.forEach(function(p) {
			var hl = p.badge_label ? ' azonmate-showcase__row--highlighted' : '';
			h += '<div class="azonmate-showcase__row' + hl + '">';
			if (p.badge_label) h += '<span class="azonmate-showcase__badge azonmate-showcase__badge--ribbon">' + esc(p.badge_label) + '</span>';
			h += '<div class="azonmate-showcase__row-image">' + savingsHtml(p, 'azonmate-showcase__savings--corner') + img(p, 'medium') + '</div>';
			h += '<div class="azonmate-showcase__row-body">' + brandHtml(p) + titleHtml(p) + ratingHtml(p) + descHtml(p, 30) + featuresHtml(p, 3) + '</div>';
			h += '<div class="azonmate-showcase__row-side">' + priceHtml(p, ' azonmate-showcase__price-wrap--vertical') + btnHtml(btn) + '</div>';
			h += '</div>';
		});
		h += '</div></div>';
		return h;
	}

	// =========================================================
	// Masonry
	// =========================================================
	function buildMasonry(prods, heading, btn, cols) {
		var c = cols || 3;
		var h = '<div class="azonmate-showcase azonmate-showcase--masonry" style="--azonmate-masonry-cols:' + c + ';">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__masonry">';
		prods.forEach(function(p) {
			h += '<div class="azonmate-showcase__masonry-item">';
			h += badgeHtml(p);
			h += '<div class="azonmate-showcase__image-wrap">' + savingsHtml(p) + img(p, 'large') + '</div>';
			h += '<div class="azonmate-showcase__content">' + brandHtml(p) + titleHtml(p, 12, ' azonmate-showcase__title--compact') + ratingHtml(p) + priceHtml(p) + descHtml(p, 15) + featuresHtml(p, 2) + btnHtml(btn) + '</div>';
			h += '</div>';
		});
		h += '</div></div>';
		return h;
	}

	// =========================================================
	// Table
	// =========================================================
	function buildTable(prods, heading, btn) {
		var h = '<div class="azonmate-showcase azonmate-showcase--table">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__table-wrap"><table class="azonmate-showcase__table"><thead><tr>';
		prods.forEach(function(p) {
			var hl = p.badge_label ? ' azonmate-showcase__th--highlight' : '';
			h += '<th class="azonmate-showcase__th' + hl + '">';
			if (p.badge_label) h += '<span class="azonmate-showcase__badge azonmate-showcase__badge--table">' + esc(p.badge_label) + '</span>';
			h += '</th>';
		});
		h += '</tr></thead><tbody>';
		// Image row
		h += '<tr class="azonmate-showcase__tr--image">';
		prods.forEach(function(p) { var hl = p.badge_label ? ' azonmate-showcase__td--highlight' : ''; h += '<td class="azonmate-showcase__td' + hl + '"><img src="' + esc(p.image_medium || p.image_small || '') + '" class="azonmate-showcase__table-image" /></td>'; });
		h += '</tr>';
		// Title row
		h += '<tr class="azonmate-showcase__tr--title">';
		prods.forEach(function(p) { var hl = p.badge_label ? ' azonmate-showcase__td--highlight' : ''; h += '<td class="azonmate-showcase__td' + hl + '">' + brandHtml(p) + '<strong class="azonmate-showcase__table-title"><a href="#">' + esc(p.title ? p.title.split(' ').slice(0,10).join(' ') : '') + '</a></strong></td>'; });
		h += '</tr>';
		// Rating row
		h += '<tr class="azonmate-showcase__tr--rating">';
		prods.forEach(function(p) { var hl = p.badge_label ? ' azonmate-showcase__td--highlight' : ''; h += '<td class="azonmate-showcase__td' + hl + '">' + (p.rating ? '<div class="azonmate-showcase__rating azonmate-showcase__rating--center">' + stars(p.rating) + '</div>' : '<span class="azonmate-showcase__na">&mdash;</span>') + '</td>'; });
		h += '</tr>';
		// Price row
		h += '<tr class="azonmate-showcase__tr--price">';
		prods.forEach(function(p) {
			var hl = p.badge_label ? ' azonmate-showcase__td--highlight' : '';
			h += '<td class="azonmate-showcase__td' + hl + '">';
			if (p.price_display) {
				h += '<span class="azonmate-showcase__price azonmate-showcase__price--large">' + esc(p.price_display) + '</span>';
				if (p.list_price_display && p.list_price_display !== p.price_display) h += '<br><span class="azonmate-showcase__old-price"><del>' + esc(p.list_price_display) + '</del></span>';
				if (p.savings_percentage) h += ' <span class="azonmate-showcase__savings azonmate-showcase__savings--inline">-' + p.savings_percentage + '%</span>';
			} else { h += '<span class="azonmate-showcase__na">&mdash;</span>'; }
			h += '</td>';
		});
		h += '</tr>';
		// Button row
		h += '<tr class="azonmate-showcase__tr--action">';
		prods.forEach(function(p) { var hl = p.badge_label ? ' azonmate-showcase__td--highlight' : ''; h += '<td class="azonmate-showcase__td' + hl + '"><a href="#" class="azonmate-buy-btn" onclick="return false;">' + esc(btn) + '</a></td>'; });
		h += '</tr>';
		h += '</tbody></table></div></div>';
		return h;
	}

	// =========================================================
	// Hero (single product)
	// =========================================================
	function buildHero(p, heading, btn) {
		var h = '<div class="azonmate-showcase azonmate-showcase--hero">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__hero">';
		h += '<div class="azonmate-showcase__hero-image">' + badgeHtml(p) + savingsHtml(p) + img(p, 'large') + '</div>';
		h += '<div class="azonmate-showcase__hero-body">' + brandHtml(p) + titleHtml(p) + ratingHtml(p) + descHtml(p, 35) + featuresHtml(p, 4);
		h += priceHtml(p).replace('azonmate-showcase__price"', 'azonmate-showcase__price azonmate-showcase__price--hero"');
		h += btnHtml(btn);
		h += '</div></div></div>';
		return h;
	}

	// =========================================================
	// Compact (single product)
	// =========================================================
	function buildCompact(p, heading, btn) {
		var h = '<div class="azonmate-showcase azonmate-showcase--compact">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__compact">';
		if (p.badge_label) h += '<span class="azonmate-showcase__badge" style="position:static;margin-right:0.5rem;">' + esc(p.badge_label) + '</span>';
		h += '<div class="azonmate-showcase__compact-image">' + img(p, 'small') + '</div>';
		h += '<div class="azonmate-showcase__compact-body">' + titleHtml(p, 12);
		h += '<div class="azonmate-showcase__compact-meta">';
		if (p.rating) h += stars(p.rating);
		if (p.price_display) { h += '<span class="azonmate-showcase__price">' + esc(p.price_display) + '</span>'; if (p.list_price_display && p.list_price_display !== p.price_display) h += '<span class="azonmate-showcase__old-price"><del>' + esc(p.list_price_display) + '</del></span>'; }
		h += '</div></div>';
		h += '<div class="azonmate-showcase__compact-side">' + btnHtml(btn) + '</div>';
		h += '</div></div>';
		return h;
	}

	// =========================================================
	// Split (single product)
	// =========================================================
	function buildSplit(p, heading, btn) {
		var h = '<div class="azonmate-showcase azonmate-showcase--split">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__split">';
		h += '<div class="azonmate-showcase__split-image">' + badgeHtml(p) + savingsHtml(p) + img(p, 'large') + '</div>';
		h += '<div class="azonmate-showcase__split-details">' + brandHtml(p) + titleHtml(p) + ratingHtml(p) + descHtml(p, 30) + featuresHtml(p, 4) + priceHtml(p) + btnHtml(btn) + '</div>';
		h += '</div></div>';
		return h;
	}

	// =========================================================
	// Deal (single product)
	// =========================================================
	function buildDeal(p, heading, btn) {
		var h = '<div class="azonmate-showcase azonmate-showcase--deal">';
		h += headingHtml(heading);
		h += '<div class="azonmate-showcase__deal">';
		h += '<div class="azonmate-showcase__deal-image">' + img(p, 'medium') + '</div>';
		h += '<div class="azonmate-showcase__deal-body">';
		if (p.badge_label) h += '<span class="azonmate-showcase__badge" style="position:static;margin-bottom:0.4rem;">' + esc(p.badge_label) + '</span>';
		h += brandHtml(p) + titleHtml(p, 14) + ratingHtml(p);
		if (p.price_display) {
			h += '<div class="azonmate-showcase__deal-prices">';
			h += '<span class="azonmate-showcase__price">' + esc(p.price_display) + '</span>';
			if (p.list_price_display && p.list_price_display !== p.price_display) h += '<span class="azonmate-showcase__old-price"><del>' + esc(p.list_price_display) + '</del></span>';
			if (p.savings_percentage) h += '<span class="azonmate-showcase__deal-save">Save ' + p.savings_percentage + '%</span>';
			h += '</div>';
		}
		h += '</div>';
		h += '<div class="azonmate-showcase__deal-side">' + btnHtml(btn) + '</div>';
		h += '</div></div>';
		return h;
	}

})();
</script>
