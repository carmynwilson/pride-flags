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
 *   [pride flag="bi" tooltip="Bi pride!"]   → custom tooltip (tooltip="false" = off)
 *   [pride flag="bi" align="center"]        → center on its own line
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
 * Render a single flag.
 *
 * When $tip is non-empty the <img> is wrapped in a tooltip span and the
 * native title attribute is dropped (so the browser's own tooltip and
 * our styled one don't both appear). The img always keeps its alt text.
 *
 * @param string $slug          Flag slug.
 * @param array  $flag          Registry entry.
 * @param string $height        CSS height value (e.g. "48px", "2rem"); '' = CSS default.
 * @param array  $extra_classes Extra CSS classes for the img.
 * @param string $label         alt-text override (empty = "{Label} pride flag").
 * @param string $tip           Tooltip text; '' = no tooltip.
 * @return string HTML, or '' if the image can't be resolved.
 */
function pride_flags_render_img( $slug, array $flag, $height = '', array $extra_classes = [], $label = '', $tip = '' ) {
	$src = pride_flags_image_url( $flag );
	if ( '' === $src ) {
		return '';
	}

	$alt = '' !== $label ? $label : $flag['label'] . ' pride flag';

	$classes = array_merge( [ 'pride-flag', 'pride-flag--' . $slug ], $extra_classes );

	$style = '';
	if ( '' !== $height ) {
		$style = sprintf( ' style="height:%s;width:auto;"', esc_attr( $height ) );
	}

	$has_tip    = ( '' !== $tip );
	$title_attr = $has_tip ? '' : sprintf( ' title="%s"', esc_attr( $alt ) );

	$img = sprintf(
		'<img src="%s" alt="%s"%s class="%s"%s loading="lazy" decoding="async" />',
		esc_url( $src ),
		esc_attr( $alt ),
		$title_attr,
		esc_attr( implode( ' ', array_filter( $classes ) ) ),
		$style
	);

	if ( ! $has_tip ) {
		return $img;
	}

	return sprintf(
		'<span class="pride-flag-tip" data-pride-tip="%s" tabindex="0">%s</span>',
		esc_attr( $tip ),
		$img
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
			'flag'    => '',   // one slug, or comma-separated slugs; empty → default
			'class'   => '',   // extra class(es) on the img (single) or wrapper (collection)
			'size'    => '',   // optional height: bare number = px, or any CSS unit (2rem/50%)
			'label'   => '',   // override alt text (single flag only); defaults to flag label
			'tooltip' => '',   // tooltip text; empty = flag name; "false"/"no"/"off" = none
			'align'   => '',   // left | center | right; anything else = inline default
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

	// Tooltip mode. Empty → use the flag name; a disable keyword → off;
	// anything else → that literal text (single flag only).
	$tip_raw      = trim( $atts['tooltip'] );
	$tooltips_off = in_array( strtolower( $tip_raw ), [ 'false', 'no', 'off', '0', 'none' ], true );

	// Build the flag markup (single img/tip-span, or a collection wrapper).
	if ( count( $slugs ) === 1 ) {
		$slug  = $slugs[0];
		$label = '' !== $atts['label'] ? $atts['label'] : '';
		$tip   = '';
		if ( ! $tooltips_off ) {
			$tip = '' !== $tip_raw ? $tip_raw : $registry[ $slug ]['label'];
		}
		$out = pride_flags_render_img( $slug, $registry[ $slug ], $height, $extra, $label, $tip );
	} else {
		$imgs = '';
		foreach ( $slugs as $slug ) {
			$tip   = $tooltips_off ? '' : $registry[ $slug ]['label'];
			$imgs .= pride_flags_render_img( $slug, $registry[ $slug ], $height, [], '', $tip );
		}
		$wrap_classes = array_merge( [ 'pride-flags' ], $extra );
		$out          = sprintf( '<span class="%s">%s</span>', esc_attr( implode( ' ', $wrap_classes ) ), $imgs );
	}

	// Optional alignment wrapper (block-level, aligns the inline flag/row).
	$align = strtolower( trim( $atts['align'] ) );
	if ( in_array( $align, [ 'left', 'center', 'right' ], true ) ) {
		$out = sprintf(
			'<span class="pride-flag-align pride-flag-align--%s">%s</span>',
			esc_attr( $align ),
			$out
		);
	}

	return $out;
}
add_shortcode( 'pride', 'pride_flags_shortcode' );
