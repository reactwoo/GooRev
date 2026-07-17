=== ReactWoo Reviews ===
Contributors: reactwoo
Tags: google reviews, testimonials, ratings, business reviews, google business profile, elementor, gutenberg, shortcode
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display Google Business reviews on your WordPress site with beautiful widgets and shortcodes. Free and Pro versions available.

== Description ==

ReactWoo Reviews (formerly Google Reviews Plugin / GooRev) lets you display Google Business Profile reviews on WordPress with Elementor, Gutenberg, shortcodes, and widgets.

= Key Features =

* Google Business Profile integration with OAuth sync
* Multiple display styles (Modern, Classic, Minimal, Corporate, Creative)
* Carousel, list, and grid layouts
* Elementor 3.5+ / 4.x widget support
* Gutenberg blocks with live preview
* Free and Pro/Enterprise licensing via ReactWoo
* Automatic updates via api.reactwoo.com

= Free Version =

* Connect with your own Google credentials or free license
* Core styles and layouts
* Shortcode, widget, Elementor, and Gutenberg
* Star rating filters and responsive design

= Pro / Enterprise =

* Advanced styles and customization
* Multi-location and analytics features
* Priority support (Enterprise)

== Installation ==

1. Upload the `reactwoo-reviews` folder to `/wp-content/plugins/`
2. Activate **ReactWoo Reviews** through the Plugins menu
3. Connect Google Business Profile under Reviews → Settings
4. Add the shortcode, block, or Elementor widget

**Upgrade note:** If you previously used the `GooRev` folder, deactivate the old plugin, install `reactwoo-reviews`, and re-activate. Options (`grp_*`), blocks (`google-reviews/*`), and Elementor widgets (`grp-reviews`) are preserved.

== Frequently Asked Questions ==

= Does this work with Elementor 4? =

Yes. Widgets register via the modern `elementor/widgets/register` API and use `register_controls()`.

= Do I need a license for updates? =

Plugin zip updates are free (catalog slug `reactwoo-reviews`). Pro features still require a Pro/Enterprise license.

== Changelog ==

= 1.1.0 =
* Product rename to ReactWoo Reviews (`reactwoo-reviews`)
* R2 / api.reactwoo.com update pipeline and in-plugin updater
* Elementor 3.5+/4.x registration best practices
* Gutenberg `block.json` metadata registration
* License slug aliases: goorev → reactwoo-reviews

= 1.0.0 =
* Initial release as Google Reviews Plugin

== Upgrade Notice ==

= 1.1.0 =
Install folder is now `reactwoo-reviews`. Re-activate after replacing the old `GooRev` directory.
