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
			'desc'  => 'Daniel Quasar\'s 2018 update to the rainbow flag. An arrow of black, brown, pink, light blue, and white pushes in from the left, pulling trans people and queer people of color to the front.',
		],
		'rainbow' => [
			'label' => 'Classic Rainbow',
			'file'  => 'rainbow.svg',
			'desc'  => 'The six-stripe rainbow most people picture when they hear "pride flag." It stands for the whole LGBTQIA+ community.',
		],
		'baker' => [
			'label' => 'Baker (1978 Original)',
			'file'  => 'baker.svg',
			'desc'  => 'Gilbert Baker\'s original 1978 design, the one that started it all. It ran eight stripes, including the hot pink and turquoise that got cut when the flag went into mass production.',
		],
		'philly-pride' => [
			'label' => 'Philadelphia Pride',
			'file'  => 'philly-pride.svg',
			'desc'  => 'Philadelphia\'s 2017 "More Color More Pride" flag. It puts black and brown stripes on top of the rainbow to make room for queer people of color.',
		],
		'two-spirit-rainbow' => [
			'label' => 'Two-Spirit (Rainbow)',
			'file'  => 'two-spirit-rainbow.jpg',
			'desc'  => 'Two-Spirit is a term some Indigenous North American communities use for people who carry both a masculine and feminine spirit. This version pairs that meaning with the rainbow.',
		],
		'two-spirit-trans' => [
			'label' => 'Two-Spirit (Trans)',
			'file'  => 'two-spirit-trans.webp',
			'desc'  => 'The same Two-Spirit meaning, here carried on the trans pride colors.',
		],
		'queer-people-of-color' => [
			'label' => 'Queer People of Color',
			'file'  => 'queer-people-of-color.jpg',
			'desc'  => 'A flag for queer people of color, naming the place where being LGBTQIA+ and being a person of color meet.',
		],

		// ── Trans family ──────────────────────────────────────────
		'trans' => [
			'label' => 'Trans',
			'file'  => 'trans.svg',
			'desc'  => 'Monica Helms made this in 1999. The blue and pink come from the old baby-color convention and the white in the middle is for nonbinary and transitioning people. It reads the same flipped either way, which was the whole idea.',
		],
		'trans-femme' => [
			'label' => 'Trans Femme',
			'file'  => 'trans-femme.svg',
			'desc'  => 'A flag for transfeminine people.',
		],
		'trans-masc' => [
			'label' => 'Trans Masc',
			'file'  => 'trans-masc.svg',
			'desc'  => 'A flag for transmasculine people.',
		],

		// ── Nonbinary / agender family ────────────────────────────
		'nonbinary' => [
			'label' => 'Nonbinary',
			'file'  => 'nonbinary.svg',
			'desc'  => 'Kye Rowan made this in 2014 for people whose gender sits outside the binary. Yellow is for genders apart from male and female, white for many or all genders, purple for a blend, and black for having none.',
		],
		'genderqueer' => [
			'label' => 'Genderqueer',
			'file'  => 'genderqueer.svg',
			'desc'  => 'A flag for genderqueer people, whose identities land between or beyond "man" and "woman." Lavender mixes blue and pink, white is for agender, and green is for everything off the binary.',
		],
		'genderfluid' => [
			'label' => 'Genderfluid',
			'file'  => 'genderfluid.svg',
			'desc'  => 'A flag for people whose gender moves and shifts over time instead of holding still.',
		],
		'agender' => [
			'label' => 'Agender',
			'file'  => 'agender.svg',
			'desc'  => 'A flag for people who have no gender or feel gender-neutral. The green stripe is the deliberate opposite of the gendered pink and blue.',
		],

		// ── Intersex ──────────────────────────────────────────────
		'intersex' => [
			'label' => 'Intersex',
			'file'  => 'intersex.svg',
			'desc'  => 'Morgan Carpenter designed this in 2013: a purple ring on a yellow field. The colors steer clear of pink and blue on purpose, and the unbroken circle is about being whole as you are.',
		],

		// ── Attraction ────────────────────────────────────────────
		'lesbian' => [
			'label' => 'Lesbian',
			'file'  => 'lesbian.svg',
			'desc'  => 'The orange-and-pink lesbian flag, Emily Gwen\'s 2018 design. The shades run from independence and community through to love and femininity.',
		],
		'bi' => [
			'label' => 'Bisexual',
			'file'  => 'bi.svg',
			'desc'  => 'Michael Page made this in 1998. Pink is attraction to the same gender, blue to a different one, and the purple where they overlap is for both at once.',
		],
		'pan' => [
			'label' => 'Pansexual',
			'file'  => 'pan.svg',
			'desc'  => 'The pansexual flag, for attraction to people of any gender. Pink, yellow, and blue cover women, everyone else, and men.',
		],
		'polysexual' => [
			'label' => 'Polysexual',
			'file'  => 'polysexual.svg',
			'desc'  => 'A flag for polysexual people, who are drawn to many genders but not necessarily all of them.',
		],

		// ── Ace / aro spectrum ────────────────────────────────────
		'ace' => [
			'label' => 'Asexual',
			'file'  => 'ace.svg',
			'desc'  => 'The asexual flag. Black is for asexuality, gray for the space in between, white for allies, and purple for the community.',
		],
		'demisexual' => [
			'label' => 'Demisexual',
			'file'  => 'demisexual.svg',
			'desc'  => 'A flag for demisexual people, who feel sexual attraction only once a real emotional connection is there.',
		],
		'aro' => [
			'label' => 'Aromantic',
			'file'  => 'aro.svg',
			'desc'  => 'Cameron Whimsy\'s 2014 aromantic flag, for people who feel little or no romantic attraction. The greens stand for aromanticism and its spectrum.',
		],
		'demiromantic' => [
			'label' => 'Demiromantic',
			'file'  => 'demiromantic.svg',
			'desc'  => 'A flag for demiromantic people, who feel romantic attraction only after an emotional bond has formed.',
		],
		'aroace' => [
			'label' => 'Aroace',
			'file'  => 'aroace.svg',
			'desc'  => 'A flag for people who are both aromantic and asexual, blending the colors of each.',
		],

		// ── Relationship structure ────────────────────────────────
		'polyamorous' => [
			'label' => 'Polyamorous',
			'file'  => 'polyamorous.svg',
			'desc'  => 'A flag for polyamory and ethical non-monogamy: loving more than one person at a time, openly and by agreement.',
		],

		// ── Allyship (a stance, not an identity, kept at the end) ──
		'ally' => [
			'label' => 'Ally',
			'file'  => 'ally.svg',
			'desc'  => 'A flag for straight and cisgender allies. The black-and-white stripes are theirs, and the rainbow chevron is the community they show up for.',
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
