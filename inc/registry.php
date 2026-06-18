<?php
/**
 * Pride Flags — Flag Registry
 *
 * The master list of flags the plugin knows about. Ported from the
 * Violet Index identity-flag library. Each entry:
 *   slug => [ 'label', 'file' (in assets/flags/), 'desc' ]
 *
 * The registry is filterable so sites can add their own flags without
 * editing the plugin:
 *
 *   add_filter( 'pride_flags_registry', function ( $flags ) {
 *       $flags['my-flag'] = [
 *           'label' => 'My Flag',
 *           'file'  => 'my-flag.svg',  // dropped in a child plugin / uploads
 *           'url'   => 'https://example.com/my-flag.svg', // optional, overrides file
 *           'desc'  => 'What this flag represents.',
 *       ];
 *       return $flags;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The default flag the shortcode falls back to when no (or an unknown)
 * flag is requested.
 */
function pride_flags_default_slug() {
	return apply_filters( 'pride_flags_default_slug', 'progress-pride' );
}

/**
 * Master registry. Order here is the order the library renders them in:
 * most-recognized umbrella flags first, then grouped by family.
 *
 * @return array<string, array{label:string, file:string, desc:string}>
 */
function pride_flags_registry() {
	$flags = [

		// ── Umbrella flags (most-recognized) ──────────────────────
		'progress-pride' => [
			'label' => 'Progress Pride',
			'file'  => 'progress-pride.svg',
			'desc'  => 'Daniel Quasar\'s 2018 redesign of the rainbow flag, adding a forward-pointing chevron of black, brown, and trans-flag stripes to center trans people and queer people of color.',
		],
		'rainbow' => [
			'label' => 'Classic Rainbow',
			'file'  => 'rainbow.svg',
			'desc'  => 'The widely-recognized six-stripe rainbow pride flag representing the LGBTQIA+ community as a whole.',
		],
		'baker' => [
			'label' => 'Baker (1978 Original)',
			'file'  => 'baker.svg',
			'desc'  => 'Gilbert Baker\'s original 1978 eight-stripe pride flag, including the hot pink and turquoise stripes later dropped from the common version.',
		],
		'philly-pride' => [
			'label' => 'Philadelphia Pride',
			'file'  => 'philly-pride.svg',
			'desc'  => 'The 2017 "More Color More Pride" flag from Philadelphia, adding black and brown stripes to honor queer people of color.',
		],
		'two-spirit-rainbow' => [
			'label' => 'Two-Spirit (Rainbow)',
			'file'  => 'two-spirit-rainbow.jpg',
			'desc'  => 'A flag for Two-Spirit people, an umbrella term used by some Indigenous North American cultures for people who hold both masculine and feminine spirits.',
		],
		'two-spirit-trans' => [
			'label' => 'Two-Spirit (Trans)',
			'file'  => 'two-spirit-trans.webp',
			'desc'  => 'A Two-Spirit variant incorporating the trans pride colors.',
		],
		'queer-people-of-color' => [
			'label' => 'Queer People of Color',
			'file'  => 'queer-people-of-color.jpg',
			'desc'  => 'A flag overlaying the rainbow with a raised fist, representing solidarity between the LGBTQIA+ community and people of color.',
		],

		// ── Trans family ──────────────────────────────────────────
		'trans' => [
			'label' => 'Trans',
			'file'  => 'trans.svg',
			'desc'  => 'Monica Helms\'s 1999 transgender pride flag — light blue, pink, and white stripes representing trans men, trans women, and nonbinary or transitioning people.',
		],
		'trans-femme' => [
			'label' => 'Trans Femme',
			'file'  => 'trans-femme.svg',
			'desc'  => 'A flag representing transfeminine identity.',
		],
		'trans-masc' => [
			'label' => 'Trans Masc',
			'file'  => 'trans-masc.svg',
			'desc'  => 'A flag representing transmasculine identity.',
		],

		// ── Nonbinary / agender family ────────────────────────────
		'nonbinary' => [
			'label' => 'Nonbinary',
			'file'  => 'nonbinary.svg',
			'desc'  => 'Kye Rowan\'s 2014 nonbinary pride flag — yellow, white, purple, and black stripes for genders outside the binary.',
		],
		'genderqueer' => [
			'label' => 'Genderqueer',
			'file'  => 'genderqueer.svg',
			'desc'  => 'A flag for genderqueer identity — lavender, white, and green stripes.',
		],
		'genderfluid' => [
			'label' => 'Genderfluid',
			'file'  => 'genderfluid.svg',
			'desc'  => 'A flag for people whose gender shifts or fluctuates over time.',
		],
		'agender' => [
			'label' => 'Agender',
			'file'  => 'agender.svg',
			'desc'  => 'A flag for people who identify as having no gender or being gender-neutral.',
		],

		// ── Intersex ──────────────────────────────────────────────
		'intersex' => [
			'label' => 'Intersex',
			'file'  => 'intersex.svg',
			'desc'  => 'Morgan Carpenter\'s 2013 intersex pride flag — a purple circle on a yellow field, deliberately free of gendered colors.',
		],

		// ── Attraction ────────────────────────────────────────────
		'lesbian' => [
			'label' => 'Lesbian',
			'file'  => 'lesbian.svg',
			'desc'  => 'The community lesbian pride flag in shades of orange, white, and pink.',
		],
		'bi' => [
			'label' => 'Bisexual',
			'file'  => 'bi.svg',
			'desc'  => 'Michael Page\'s 1998 bisexual pride flag — pink, purple, and blue stripes.',
		],
		'pan' => [
			'label' => 'Pansexual',
			'file'  => 'pan.svg',
			'desc'  => 'The pansexual pride flag — pink, yellow, and blue stripes, for attraction regardless of gender.',
		],
		'polysexual' => [
			'label' => 'Polysexual',
			'file'  => 'polysexual.svg',
			'desc'  => 'A flag for polysexual people — those attracted to multiple, but not necessarily all, genders.',
		],

		// ── Ace / aro spectrum ────────────────────────────────────
		'ace' => [
			'label' => 'Asexual',
			'file'  => 'ace.svg',
			'desc'  => 'The asexual pride flag — black, grey, white, and purple stripes for the asexual spectrum.',
		],
		'demisexual' => [
			'label' => 'Demisexual',
			'file'  => 'demisexual.svg',
			'desc'  => 'A flag for demisexual people, who experience sexual attraction only after forming a strong emotional bond.',
		],
		'aro' => [
			'label' => 'Aromantic',
			'file'  => 'aro.svg',
			'desc'  => 'The aromantic pride flag — green, light green, white, grey, and black stripes for the aromantic spectrum.',
		],
		'demiromantic' => [
			'label' => 'Demiromantic',
			'file'  => 'demiromantic.svg',
			'desc'  => 'A flag for demiromantic people, who experience romantic attraction only after forming a strong emotional bond.',
		],
		'aroace' => [
			'label' => 'Aroace',
			'file'  => 'aroace.svg',
			'desc'  => 'A flag for people who are both aromantic and asexual.',
		],

		// ── Relationship structure ────────────────────────────────
		'polyamorous' => [
			'label' => 'Polyamorous',
			'file'  => 'polyamorous.svg',
			'desc'  => 'A flag representing polyamory and consensual non-monogamy.',
		],

		// ── Allyship (a stance, not an identity — kept at the end) ─
		'ally' => [
			'label' => 'Ally',
			'file'  => 'ally.svg',
			'desc'  => 'A flag for straight and cisgender allies of the LGBTQIA+ community — a rainbow chevron on black-and-white stripes.',
		],
	];

	/**
	 * Filter the flag registry. See file header for the entry shape.
	 */
	return apply_filters( 'pride_flags_registry', $flags );
}

/**
 * Resolve the public image URL for a flag entry. Honors an explicit
 * 'url' override (for filter-added flags hosted elsewhere); otherwise
 * builds the URL from the bundled 'file'.
 *
 * @param array $flag A registry entry.
 * @return string
 */
function pride_flags_image_url( array $flag ) {
	if ( ! empty( $flag['url'] ) ) {
		return $flag['url'];
	}
	if ( ! empty( $flag['file'] ) ) {
		return PRIDE_FLAGS_URL . 'assets/flags/' . ltrim( $flag['file'], '/' );
	}
	return '';
}
