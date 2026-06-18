<?php
/**
 * Pride Flags — Admin Library reference page
 *
 * A dashboard page (Pride Flags menu) that lists every registered flag
 * with its image, label, slug, description, and a copy-to-clipboard
 * button for the matching [pride flag="…"] shortcode. Searchable.
 *
 * Logged-in editors only — capability: edit_posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the top-level admin menu page.
 */
function pride_flags_admin_menu() {
	$hook = add_menu_page(
		__( 'Pride Flags', 'pride-flags' ),
		__( 'Pride Flags', 'pride-flags' ),
		'edit_posts',
		'pride-flags',
		'pride_flags_render_library_page',
		'dashicons-heart',
		81
	);
	add_action( 'admin_print_styles-' . $hook, 'pride_flags_admin_assets' );
	add_action( 'admin_print_footer_scripts-' . $hook, 'pride_flags_admin_inline_js' );
}
add_action( 'admin_menu', 'pride_flags_admin_menu' );

/**
 * Enqueue the admin stylesheet only on our page.
 */
function pride_flags_admin_assets() {
	wp_enqueue_style(
		'pride-flags-admin',
		PRIDE_FLAGS_URL . 'assets/admin/admin.css',
		[],
		PRIDE_FLAGS_VERSION
	);
}

/**
 * Render the library page.
 */
function pride_flags_render_library_page() {
	$registry = pride_flags_registry();
	$default  = pride_flags_default_slug();
	?>
	<div class="wrap pride-flags-wrap">
		<h1><?php esc_html_e( 'Pride Flags', 'pride-flags' ); ?></h1>
		<p class="pride-flags-intro">
			<?php
			printf(
				/* translators: %s: shortcode example */
				esc_html__( 'Drop any flag into a post or page with the %s shortcode. With no flag (or an unknown one) it renders the Progress Pride flag. Click "Copy" on any card to grab its shortcode.', 'pride-flags' ),
				'<code>[pride flag=&quot;trans&quot;]</code>'
			);
			?>
		</p>
		<p class="pride-flags-intro pride-flags-intro--muted">
			<?php esc_html_e( 'You can also add a CSS class or a pixel height:', 'pride-flags' ); ?>
			<code>[pride flag=&quot;nonbinary&quot; class=&quot;my-class&quot; size=&quot;48&quot;]</code>
		</p>

		<p class="pride-flags-search">
			<label class="screen-reader-text" for="pride-flags-search"><?php esc_html_e( 'Search flags', 'pride-flags' ); ?></label>
			<input type="search" id="pride-flags-search" class="regular-text"
				placeholder="<?php esc_attr_e( 'Search flags by name or slug…', 'pride-flags' ); ?>" autocomplete="off">
		</p>

		<div class="pride-flags-grid" id="pride-flags-grid">
			<?php foreach ( $registry as $slug => $flag ) :
				$src       = pride_flags_image_url( $flag );
				$shortcode = '[pride flag="' . $slug . '"]';
				$is_default = ( $slug === $default );
				$search    = strtolower( $flag['label'] . ' ' . $slug );
				?>
				<div class="pride-flags-card" data-search="<?php echo esc_attr( $search ); ?>">
					<div class="pride-flags-card__media">
						<?php if ( $src ) : ?>
							<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $flag['label'] . ' pride flag' ); ?>">
						<?php endif; ?>
					</div>
					<div class="pride-flags-card__body">
						<h2 class="pride-flags-card__label">
							<?php echo esc_html( $flag['label'] ); ?>
							<?php if ( $is_default ) : ?>
								<span class="pride-flags-badge"><?php esc_html_e( 'default', 'pride-flags' ); ?></span>
							<?php endif; ?>
						</h2>
						<?php if ( ! empty( $flag['desc'] ) ) : ?>
							<p class="pride-flags-card__desc"><?php echo esc_html( $flag['desc'] ); ?></p>
						<?php endif; ?>
						<div class="pride-flags-card__shortcode">
							<code class="pride-flags-code"><?php echo esc_html( $shortcode ); ?></code>
							<button type="button" class="button button-small pride-flags-copy"
								data-clipboard="<?php echo esc_attr( $shortcode ); ?>">
								<?php esc_html_e( 'Copy', 'pride-flags' ); ?>
							</button>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="pride-flags-noresults" id="pride-flags-noresults" hidden>
			<?php esc_html_e( 'No flags match your search.', 'pride-flags' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Inline footer JS: live search filter + copy-to-clipboard. Kept inline
 * (and tiny) so the page ships no extra HTTP request.
 */
function pride_flags_admin_inline_js() {
	?>
	<script>
	( function () {
		var search = document.getElementById( 'pride-flags-search' );
		var grid   = document.getElementById( 'pride-flags-grid' );
		var none   = document.getElementById( 'pride-flags-noresults' );
		if ( ! grid ) { return; }
		var cards = Array.prototype.slice.call( grid.querySelectorAll( '.pride-flags-card' ) );

		if ( search ) {
			search.addEventListener( 'input', function () {
				var q = this.value.trim().toLowerCase();
				var shown = 0;
				cards.forEach( function ( card ) {
					var match = ! q || card.getAttribute( 'data-search' ).indexOf( q ) !== -1;
					card.hidden = ! match;
					if ( match ) { shown++; }
				} );
				if ( none ) { none.hidden = shown !== 0; }
			} );
		}

		grid.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest ? e.target.closest( '.pride-flags-copy' ) : null;
			if ( ! btn ) { return; }
			var text = btn.getAttribute( 'data-clipboard' );
			var done = function () {
				var label = btn.textContent;
				btn.textContent = '<?php echo esc_js( __( 'Copied!', 'pride-flags' ) ); ?>';
				btn.classList.add( 'pride-flags-copy--done' );
				setTimeout( function () {
					btn.textContent = label;
					btn.classList.remove( 'pride-flags-copy--done' );
				}, 1500 );
			};
			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then( done, function () { fallbackCopy( text, done ); } );
			} else {
				fallbackCopy( text, done );
			}
		} );

		function fallbackCopy( text, done ) {
			var ta = document.createElement( 'textarea' );
			ta.value = text;
			ta.setAttribute( 'readonly', '' );
			ta.style.position = 'absolute';
			ta.style.left = '-9999px';
			document.body.appendChild( ta );
			ta.select();
			try { document.execCommand( 'copy' ); done(); } catch ( err ) {}
			document.body.removeChild( ta );
		}
	} )();
	</script>
	<?php
}
