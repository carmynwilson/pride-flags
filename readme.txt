=== Pride Flags ===
Contributors: carmynwilson
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 8.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple [pride flag=""] shortcode that renders pride & identity flags, with a built-in dashboard library.

== Description ==

Pride Flags adds a single, easy shortcode for dropping pride and identity flags into any post or page:

    [pride flag="trans"]

With no flag — or an unrecognized one — it renders the Progress Pride flag, so the shortcode never comes up empty.

**Attributes**

* `flag` — the flag slug (e.g. `trans`, `nonbinary`, `bi`). Defaults to `progress-pride`.
* `class` — extra CSS class(es) appended to the rendered flag.
* `size` — optional rendered height in pixels (e.g. `48`).
* `label` — override the alt/title text (defaults to "{Flag} pride flag").

**Library**

A **Pride Flags** page in the WordPress dashboard lists every flag with its name, slug, description, and a one-click "Copy" button for the matching shortcode. Searchable by name or slug.

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

= 1.0.0 - 2026-06-18 =
* Initial build.
* `[pride flag="…"]` shortcode with `class`, `size`, and `label` attributes; defaults to Progress Pride.
* 26 bundled flags (Progress Pride, Trans, and the full Violet Index identity-flag set).
* Searchable dashboard library with copy-to-clipboard shortcodes.
* Filterable registry (`pride_flags_registry`, `pride_flags_default_slug`).
* GitHub updates via Plugin Update Checker.
