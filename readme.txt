=== Pride Flags ===
Contributors: carmynwilson
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 1.1.0
Requires PHP: 8.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple [pride flag=""] shortcode that renders pride & identity flags, with a built-in dashboard library.

== Description ==

Pride Flags adds a single, easy shortcode for dropping pride and identity flags into any post or page:

    [pride flag="trans"]

With no flag, or an unrecognized one, it renders the Progress Pride flag, so the shortcode never comes up empty.

You can also render a collection by passing several slugs, comma-separated:

    [pride flag="trans,nonbinary,bi"]

**Attributes**

* `flag` — one flag slug, or a comma-separated list for a collection (e.g. `trans`, or `trans,nonbinary,bi`). Defaults to `progress-pride`.
* `class` — extra CSS class(es). Applied to the flag (single) or the wrapping row (collection).
* `size` — optional rendered height. A bare number is treated as pixels (`48`), or pass any CSS unit (`2rem`, `1.5em`, `50%`, `3vh`).
* `label` — override the alt text on a single flag (defaults to "{Flag} pride flag").
* `tooltip` — hover/focus tooltip text. Defaults to the flag name; pass your own text for a single flag, or `tooltip="false"` to turn it off.
* `align` — `left`, `center`, or `right` to align the flag (or collection) on its own line. Omit for inline flow with surrounding text.

**Library + collection builder**

A **Pride Flags** page in the WordPress dashboard lists every flag with its name, slug, description, and a one-click "Copy" button for the matching shortcode. Searchable by name or slug.

The same page has a **collection builder**: click "Add" on any flags to stack them, then copy a single `[pride flag="…"]` shortcode that renders the whole set in a row.

**Extending**

The flag registry is filterable — add your own without editing the plugin:

    add_filter( 'pride_flags_registry', function ( $flags ) {
        $flags['my-flag'] = [
            'label' => 'My Flag',
            'url'   => 'https://example.com/my-flag.svg',
            'desc'  => 'What this flag represents.',
        ];
        return $flags;
    } );

The default fallback flag is filterable too via `pride_flags_default_slug`.

Flag artwork is bundled with the plugin (ported from the Violet Index identity-flag library).

== Changelog ==

= 1.1.0 - 2026-06-18 =
* Collections: the `flag` attribute now accepts a comma-separated list (e.g. `trans,nonbinary,bi`) to render a row of flags.
* Added a point-and-click collection builder to the dashboard library: add flags, see chips, copy one combined shortcode.
* `size` now accepts any CSS unit (`2rem`, `1.5em`, `50%`, `3vh`); a bare number still means pixels.
* Tooltips: flags now show a styled hover/focus tooltip with the flag name by default. Customize with `tooltip="…"` or disable with `tooltip="false"`.
* New `align` attribute (`left`/`center`/`right`) to place a flag or collection on its own line.
* A touch more spacing between flags in a collection.

= 1.0.0 - 2026-06-18 =
* Initial build.
* `[pride flag="…"]` shortcode with `class`, `size`, and `label` attributes; defaults to Progress Pride.
* 26 bundled flags (Progress Pride, Trans, and the full Violet Index identity-flag set).
* Searchable dashboard library with copy-to-clipboard shortcodes.
* Filterable registry (`pride_flags_registry`, `pride_flags_default_slug`).
* GitHub updates via Plugin Update Checker.
