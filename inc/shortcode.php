<?php
/**
 * Pride Flags — [pride] shortcode
 *
 * Usage:
 *   [pride]                                 → Progress Pride (default)
 *   [pride flag="trans"]                    → Trans flag
 *   [pride flag="trans,nonbinary,bi"]       → a row of flags (a "collection")
 *   [pride flag="nonbinary" class="big"]    → extra CSS class
 *   [pride flag="bi" size="48"]             → set height (bare number = px)
 *   [pride flag="bi" size="2rem"]           → height in any CSS unit (rem/em/%/vh…)
 *
 * The flag attribute takes one slug or a comma-separated list. Unknown
 * slugs are dropped; if nothing valid is left, it falls back to the
 * default flag (Progress Pride) so the shortcode never renders nothing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize the `size` attribute into a CSS height value.
 *
 * Accepts a bare number (treated as px) or a number with a CSS length
 * unit (px, em, rem, %, vh, vw, pt, etc.). Anything else returns '' so
 * the flag falls back to its CSS default size.
 *
 * @param string $raw Raw attribute value, e.g. "48", "2rem", "50%".
 * @return string A CSS length like "48px" / "2rem", or '' if invalid.
 */
function pride_flags_sanitize_size( $raw ) {
	$raw = strtolower( trim( (string) $raw ) );
	if ( '' === $raw ) {
		return '';
	}
	// Capture a positive number, then an optional CSS length unit.
	if ( ! preg_match( '/^(\d*\.?\d+)\s*(px|em|rem|%|vh|vw|vmin|vmax|pt|pc|cm|mm|in|ex|ch)?$/', $raw, $m ) ) {
		return '';
	}
	if ( (float) $m[1] <= 0 ) {
		return '';
	}
	$unit = ( isset( $m[2] ) && '' !== $m[2] ) ? $m[2] : 'px'; // bare number → px
	return $m[1] . $unit;
}

/**
 * Render a single <img> for one flag.
 *
 * @param string $slug          Flag slug.
 * @param array  $flag          Registry entry.
 * @param string $height        CSS height value (e.g. "48px", "2rem"); '' = CSS default.
 * @param array  $extra_classes Extra CSS classes for the img.
 * @param string $label         alt/title override (empty = "{Label} pride flag").
 * @return string HTML, or '' if the image can't be resolved.
 */
function pride_flags_render_img( $slug, array $flag, $height = '', array $extra_classes = [], $label = '' ) {
	$src = pride_flags_image_url( $flag );
	if ( '' === $src ) {
		return '';
	}

	if ( '' === $label ) {
		$label = $flag['label'] . ' pride flag';
	}

	$classes = array_merge( [ 'pride-flag', 'pride-flag--' . $slug ], $extra_classes );

	$style = '';
	if ( '' !== $height ) {
		$style = sprintf( ' style="height:%s;width:auto;"', esc_attr( $height ) );
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

/**
 * Render one or more flags from the [pride] shortcode.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Unused.
 * @return string HTML.
 */
function pride_flags_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts(
		[
			'flag'  => '',     // one slug, or comma-separated slugs; empty → default
			'class' => '',     // extra class(es) on the img (single) or wrapper (collection)
			'size'  => '',     // optional height in px (e.g. "48")
			'label' => '',     // override alt/title (single flag only); defaults to flag label
		],
		$atts,
		'pride'
	);

	$registry = pride_flags_registry();

	// Parse the flag attribute into a clean, validated, ordered slug list.
	$requested = array_filter( array_map( 'sanitize_key', array_map( 'trim', explode( ',', $atts['flag'] ) ) ) );
	$slugs     = array_values( array_filter( $requested, function ( $s ) use ( $registry ) {
		return isset( $registry[ $s ] );
	} ) );

	// Nothing valid requested → fall back to the default flag.
	if ( empty( $slugs ) ) {
		$default = pride_flags_default_slug();
		if ( isset( $registry[ $default ] ) ) {
			$slugs = [ $default ];
		}
	}
	if ( empty( $slugs ) ) {
		return '';
	}

	if ( ! is_admin() ) {
		wp_enqueue_style( 'pride-flags' );
	}

	$height = pride_flags_sanitize_size( $atts['size'] );

	// Normalize caller-supplied classes.
	$extra = [];
	if ( '' !== trim( $atts['class'] ) ) {
		foreach ( preg_split( '/\s+/', trim( $atts['class'] ) ) as $c ) {
			$cc = sanitize_html_class( $c );
			if ( '' !== $cc ) {
				$extra[] = $cc;
			}
		}
	}

	// Single flag → return the bare <img> (classes + label apply to it).
	if ( count( $slugs ) === 1 ) {
		$slug  = $slugs[0];
		$label = '' !== $atts['label'] ? $atts['label'] : '';
		return pride_flags_render_img( $slug, $registry[ $slug ], $height, $extra, $label );
	}

	// Collection → wrap the imgs in a group span; caller classes go on the wrapper.
	$imgs = '';
	foreach ( $slugs as $slug ) {
		$imgs .= pride_flags_render_img( $slug, $registry[ $slug ], $height );
	}

	$wrap_classes = array_merge( [ 'pride-flags' ], $extra );
	return sprintf(
		'<span class="%s">%s</span>',
		esc_attr( implode( ' ', $wrap_classes ) ),
		$imgs
	);
}
add_shortcode( 'pride', 'pride_flags_shortcode' );
