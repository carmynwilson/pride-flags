<?php
/**
 * Pride Flags — [pride] shortcode
 *
 * Usage:
 *   [pride]                              → Progress Pride (default)
 *   [pride flag="trans"]                 → Trans flag
 *   [pride flag="nonbinary" class="big"] → Nonbinary flag with an extra class
 *   [pride flag="bi" size="48"]          → set the rendered height in px
 *
 * Any unknown or empty flag falls back to the default (Progress Pride),
 * so the shortcode never renders nothing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a single flag image.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Unused.
 * @return string HTML.
 */
function pride_flags_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts(
		[
			'flag'  => '',     // slug; empty → default
			'class' => '',     // extra class(es) appended to the wrapper
			'size'  => '',     // optional height in px (e.g. "48")
			'label' => '',     // override alt/title text; defaults to flag label
		],
		$atts,
		'pride'
	);

	$registry = pride_flags_registry();

	// Resolve the requested slug, falling back to the default flag.
	$slug = sanitize_key( $atts['flag'] );
	if ( '' === $slug || ! isset( $registry[ $slug ] ) ) {
		$slug = pride_flags_default_slug();
	}

	// If even the default is missing (registry filtered oddly), bail safely.
	if ( ! isset( $registry[ $slug ] ) ) {
		return '';
	}

	$flag = $registry[ $slug ];
	$src  = pride_flags_image_url( $flag );
	if ( '' === $src ) {
		return '';
	}

	// Make sure the front-end style is on the page.
	if ( ! is_admin() ) {
		wp_enqueue_style( 'pride-flags' );
	}

	$label = '' !== $atts['label'] ? $atts['label'] : ( $flag['label'] . ' pride flag' );

	// Wrapper classes: base + slug modifier + caller-supplied classes.
	$classes = [ 'pride-flag', 'pride-flag--' . $slug ];
	if ( '' !== trim( $atts['class'] ) ) {
		foreach ( preg_split( '/\s+/', trim( $atts['class'] ) ) as $c ) {
			$classes[] = sanitize_html_class( $c );
		}
	}

	// Optional explicit height.
	$style = '';
	$size  = (int) $atts['size'];
	if ( $size > 0 ) {
		$style = sprintf( ' style="height:%dpx;width:auto;"', $size );
	}

	return sprintf(
		'<img src="%s" alt="%s" title="%s" class="%s"%s loading="lazy" decoding="async" />',
		esc_url( $src ),
		esc_attr( $label ),
		esc_attr( $label ),
		esc_attr( implode( ' ', array_filter( $classes ) ) ),
		$style
	);
}
add_shortcode( 'pride', 'pride_flags_shortcode' );
