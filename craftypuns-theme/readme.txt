=== Crafty Puns ===
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Version: 1.0.0
License: GPLv2 or later

A topical-authority theme for Crafty Puns, generated from craftypuns-topical-map.xlsx
following Koray Tugberk Gubur's Semantic SEO / Topical Authority method.

== Recommended plugins ==

* Install Rank Math OR Yoast SEO for the XML sitemap. This theme reads/writes both
  plugins' title and meta-description postmeta fields (plus its own fallback), but a
  theme-only sitemap is not reliable without a plugin (or WP-Cron) handling it — see
  inc/seo-meta.php.
* Installing either plugin is optional for everything else: the theme's own
  title/meta-description/OG/canonical output (inc/seo-meta.php) and JSON-LD
  (inc/schema.php) work standalone and automatically defer to Rank Math/Yoast
  if either is active.

== Legal pages ==

Privacy Policy, Terms & Conditions, Disclaimer and Cookie Policy are shipped as
complete, usable boilerplate for a humor site running ads and a free tool — written
to be functional out of the box, NOT reviewed by a lawyer. Have a lawyer review all
four before launch.

== Placeholders to fill in before launch ==

* Contact email and DMCA takedown email are set to placeholder@craftypuns.net
  throughout (Contact page, DMCA page, footer). Replace with the real address.
* "Our Authors" uses generic "Staff Writer" placeholders — no real names invented.
* Facebook/Instagram footer social links are omitted (marked "Add if active" in the
  Header & Footer sheet) — add them in inc/fallback-menus.php once those accounts exist.
* screenshot.png is a plain placeholder graphic, not a real screenshot of the built site.

== Content status ==

See SETUP.md for the full list of which pages have real, original pun content
(Priority P1 rows) vs. which sections still contain a `<!-- TODO: puns content -->`
placeholder to be written before publishing.
