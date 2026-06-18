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
			<?php esc_html_e( 'You can also add a CSS class or a size (a bare number is pixels, or use any unit like 2rem / 50%):', 'pride-flags' ); ?>
			<code>[pride flag=&quot;nonbinary&quot; class=&quot;my-class&quot; size=&quot;2rem&quot;]</code>
		</p>

		<div class="pride-flags-builder" id="pride-flags-builder">
			<h2 class="pride-flags-builder__title"><?php esc_html_e( 'Build a collection', 'pride-flags' ); ?></h2>
			<p class="pride-flags-builder__help">
				<?php esc_html_e( 'Click "Add" on any flag below to stack it here, then copy one shortcode that renders them all in a row. Click a chip to remove it.', 'pride-flags' ); ?>
			</p>
			<div class="pride-flags-builder__chips" id="pride-flags-builder-chips" aria-live="polite">
				<span class="pride-flags-builder__empty" id="pride-flags-builder-empty"><?php esc_html_e( 'No flags added yet.', 'pride-flags' ); ?></span>
			</div>
			<div class="pride-flags-builder__output">
				<code class="pride-flags-code" id="pride-flags-builder-code">[pride flag=&quot;&quot;]</code>
				<button type="button" class="button button-primary pride-flags-builder-copy" id="pride-flags-builder-copy" disabled>
					<?php esc_html_e( 'Copy collection', 'pride-flags' ); ?>
				</button>
				<button type="button" class="button pride-flags-builder-clear" id="pride-flags-builder-clear" disabled>
					<?php esc_html_e( 'Clear', 'pride-flags' ); ?>
				</button>
			</div>
		</div>

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
				<div class="pride-flags-card" data-search="<?php echo esc_attr( $search ); ?>"
					data-slug="<?php echo esc_attr( $slug ); ?>"
					data-label="<?php echo esc_attr( $flag['label'] ); ?>"
					data-img="<?php echo esc_url( $src ); ?>">
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
							<button type="button" class="button button-small pride-flags-add" data-slug="<?php echo esc_attr( $slug ); ?>">
								<?php esc_html_e( '+ Add', 'pride-flags' ); ?>
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
		var COPIED = '<?php echo esc_js( __( 'Copied!', 'pride-flags' ) ); ?>';
		var ADDED  = '<?php echo esc_js( __( 'Added', 'pride-flags' ) ); ?>';
		var ADD    = '<?php echo esc_js( __( '+ Add', 'pride-flags' ) ); ?>';

		var grid = document.getElementById( 'pride-flags-grid' );
		if ( ! grid ) { return; }
		var cards = Array.prototype.slice.call( grid.querySelectorAll( '.pride-flags-card' ) );

		/* ── Map slug → { label, img } for chip rendering ── */
		var FLAGS = {};
		cards.forEach( function ( card ) {
			FLAGS[ card.getAttribute( 'data-slug' ) ] = {
				label: card.getAttribute( 'data-label' ),
				img:   card.getAttribute( 'data-img' )
			};
		} );

		/* ── Clipboard helper ── */
		function copyText( text, done ) {
			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then( done, function () { fallbackCopy( text, done ); } );
			} else {
				fallbackCopy( text, done );
			}
		}
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
		function flash( btn, msg ) {
			var orig = btn.textContent;
			btn.textContent = msg;
			btn.classList.add( 'pride-flags-copy--done' );
			setTimeout( function () {
				btn.textContent = orig;
				btn.classList.remove( 'pride-flags-copy--done' );
			}, 1500 );
		}

		/* ── Live search ── */
		var search = document.getElementById( 'pride-flags-search' );
		var none   = document.getElementById( 'pride-flags-noresults' );
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

		/* ── Collection builder ── */
		var collection = [];
		var chipsBox = document.getElementById( 'pride-flags-builder-chips' );
		var emptyMsg = document.getElementById( 'pride-flags-builder-empty' );
		var codeEl   = document.getElementById( 'pride-flags-builder-code' );
		var copyBtn  = document.getElementById( 'pride-flags-builder-copy' );
		var clearBtn = document.getElementById( 'pride-flags-builder-clear' );

		function buildShortcode() {
			return '[pride flag="' + collection.join( ',' ) + '"]';
		}

		function syncAddButtons() {
			cards.forEach( function ( card ) {
				var btn = card.querySelector( '.pride-flags-add' );
				if ( ! btn ) { return; }
				var inSet = collection.indexOf( card.getAttribute( 'data-slug' ) ) !== -1;
				btn.disabled = inSet;
				btn.textContent = inSet ? ADDED : ADD;
			} );
		}

		function renderBuilder() {
			// Chips
			chipsBox.querySelectorAll( '.pride-flags-chip' ).forEach( function ( c ) { c.remove(); } );
			collection.forEach( function ( slug ) {
				var f = FLAGS[ slug ] || { label: slug, img: '' };
				var chip = document.createElement( 'button' );
				chip.type = 'button';
				chip.className = 'pride-flags-chip';
				chip.setAttribute( 'data-slug', slug );
				chip.title = 'Remove ' + f.label;
				chip.innerHTML =
					( f.img ? '<img src="' + f.img + '" alt="">' : '' ) +
					'<span>' + f.label + '</span><span class="pride-flags-chip__x" aria-hidden="true">&times;</span>';
				chipsBox.appendChild( chip );
			} );
			if ( emptyMsg ) { emptyMsg.style.display = collection.length ? 'none' : ''; }

			// Output + button state
			var has = collection.length > 0;
			codeEl.textContent = has ? buildShortcode() : '[pride flag=""]';
			copyBtn.disabled = ! has;
			clearBtn.disabled = ! has;

			syncAddButtons();
		}

		function add( slug ) {
			if ( ! slug || ! FLAGS[ slug ] ) { return; }
			if ( collection.indexOf( slug ) === -1 ) {
				collection.push( slug );
				renderBuilder();
			}
		}
		function remove( slug ) {
			var i = collection.indexOf( slug );
			if ( i !== -1 ) {
				collection.splice( i, 1 );
				renderBuilder();
			}
		}

		// Add (from cards) + single-copy (from cards)
		grid.addEventListener( 'click', function ( e ) {
			var addBtn = e.target.closest ? e.target.closest( '.pride-flags-add' ) : null;
			if ( addBtn ) {
				add( addBtn.closest( '.pride-flags-card' ).getAttribute( 'data-slug' ) );
				return;
			}
			var copyBtnCard = e.target.closest ? e.target.closest( '.pride-flags-copy' ) : null;
			if ( copyBtnCard ) {
				copyText( copyBtnCard.getAttribute( 'data-clipboard' ), function () { flash( copyBtnCard, COPIED ); } );
			}
		} );

		// Remove (click a chip)
		if ( chipsBox ) {
			chipsBox.addEventListener( 'click', function ( e ) {
				var chip = e.target.closest ? e.target.closest( '.pride-flags-chip' ) : null;
				if ( chip ) { remove( chip.getAttribute( 'data-slug' ) ); }
			} );
		}

		if ( copyBtn ) {
			copyBtn.addEventListener( 'click', function () {
				if ( ! collection.length ) { return; }
				copyText( buildShortcode(), function () { flash( copyBtn, COPIED ); } );
			} );
		}
		if ( clearBtn ) {
			clearBtn.addEventListener( 'click', function () {
				collection = [];
				renderBuilder();
			} );
		}
	} )();
	</script>
	<?php
}
