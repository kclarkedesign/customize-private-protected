=== Customize Private & Protected - Change or remove title prefix and more  ===
Plugin Name: Customize Private & Protected
Contributors: kirkclarke
Donate link: https://www.paypal.com/paypalme/KirkClarke
Tags: private, password protected, widget, prefix, remove
Requires at least: 5.8
Tested up to: 7.1
Stable tag: 1.5.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Use WP Customize to modify elements of password protected and private posts and pages.

== Description ==

Use this plugin to hide or edit the prefix on your password protected or private page, add widget areas before and after the password protected form, and modify the label and submit button text. These changes are global and apply to all protected and private content respectively.

Configure everything through the Customizer (Appearance > Customize) or through Settings > Private & Protected, a plain settings page for block themes, which hide the Customizer. Both edit the same settings.

== Installation ==

Through wordpress admin:

1. Go to Plugins -> Add New
1. Search for “Customize Private & Protected”
1. Install the plugin
1. Activate it

Through FTP:

Download the plugin
Unzip customize-private-protected.zip and upload the folder to the /wp-content/plugins/ directory
Activate the plugin through the ‘Plugins’ menu in WordPress

== Frequently Asked Questions ==
	
= How to use? =
	
After activation, use the WordPress Theme customizer (Dashboard > Appearance > Customize OR "Theme Customizer" in the WP admin bar), or Dashboard > Settings > Private & Protected if your theme hides the Customizer. For password protected pages, the widgets areas are found in the widgets section of the customizer.

== Screenshots ==

1. Modify settings using the customizer.
2. For password protected pages/posts, two widget areas are added.
3. Customize as you like.

== Donations ==

If you'd like to support future development, [buy me a tea](https://www.paypal.com/paypalme/KirkClarke)!

== Changelog ==

= 1.5.0 - 09-27-2026 =
- Feature - Added a Settings page under Settings > Private & Protected, with every option the Customizer has. Block themes (the default since WordPress 6.x) hide Appearance > Customize, so this makes every option reachable without it
- Both places edit the same settings, so use whichever is easier

= 1.4.0 - 09-27-2026 =
- Feature - Added Customizer color pickers for the submit button's background and text color
- Fix - Live preview: the private-post title prefix preview rendered nothing (missing return); the protected-post one rendered the wrong thing entirely (a format string, not the title). Both properly re-render the title now, which also fixes the live preview causing a full form reload while typing
- Fix - Live preview JS: a broken selector meant it never found the title in block themes; an accidental global variable
- Fix - Checkboxes and pixel-padding fields were sanitized with a text/HTML sanitizer instead of a boolean or number one
- Fix - Prefix filters now use the post WordPress passes them, instead of only the currently-queried global post (matters for titles shown in menus, widgets, and lists)
- Fix - The `is-protected`/`is-private` body classes and the frontend stylesheet no longer apply to archive pages just because the first listed post happens to be private or protected
- Fix - The "leave a review" admin notice's role check was broken (checked role names WordPress doesn't use) and dismissing it never stuck; both fixed
- Fix - Default values now match between the Customizer and the front end, so nothing changes until a value is actually saved
- Renamed an internal class out of WordPress core's own naming space, to avoid ever colliding with it; removed a leftover unused function
- Assets are now cache-busted with the plugin version, and the plugin's stylesheet only loads on private/protected pages instead of every page

= 1.3.6 - 09-27-2026 =
- Fix - Escaped two unescaped output points in the Customizer's custom control (flagged by WordPress Plugin Check): a control's description text, and its `aria-describedby` attribute, which was also outputting a bare unwrapped ID instead of a real attribute
- First release shipped via the new automated GitHub Actions → SVN pipeline

= 1.3.5 - 09-27-2026 =
- Tested - Passed tests with WordPress version 7.1
- Fix - A `%` character in a custom title prefix no longer breaks the page on PHP 8
- Fix - Password field no longer caps entries at 20 characters (core allows up to 255)
- Fix - Password form no longer emits a duplicate `class` attribute, so theme styles for `.post-password-form` apply again
- Fix - Password form now shows the "Invalid password" message and preserves the redirect after a wrong attempt, matching WordPress core since 6.8
- Feature - Added Customizer color pickers for the password field's background and text color, to fix white-on-white fields on some themes

= 1.3.4 - 10-04-2025 =
- Tested - Passed tests with WordPress version 6.8.3

= 1.3.3 - 01-18-2025 =
- Tested - Passed tests with WordPress version 6.7.1
- Fix - Added div element to clear float from form submit button

= 1.3.2 - 05-19-2024 =
- Tested - Passed tests with WordPress version 6.5.3

= 1.3.1 - 01-13-2024 =
- Tested - Passed tests with WordPress version 6.4.2
- Tested - Passed tests with PHP versions up to 8.1.23

= 1.3.0 - 07-03-2023 =
- Enhancement - Tweaked selective refresh to improve user experience for supported themes
- Enhancement - Text domain namespace added for internationalization support

= 1.2.0 - 04-15-2023 =
- Enhancement - Implemented selective refresh to improve user experience

= 1.1.1 - 03-18-2023 =
- Tested - Passed tests with WordPress version 6.2
- Tested - Passed tests with PHP versions up to 8.1.9
- Fix - Fixed variables for custom input attributes, which broke in PHP 8 and up.

= 1.1.0 - 01-16-2023 =
- Tested - Passed test with WordPress version 6.1.1
- Feature - Added ability to customize protected button appearance
- Enhancement - Enabled translation in plugin labels
- Enhancement - Minor cleanup

= 1.0.1 - 09-24-2022 =
- Tested - Passed test with WordPress version 6.0.2
- Enhancement - Added dismissible notice for plugin review

= 1.0.0 - 04-06-2022 =
- Tested - Passed test with WordPress version 5.9.3
- Enhancement - Updated default values for button and label

= 0.2.0 - 01-09-2022 =
- Enhancement - Made fields hide/show based on hide Prefix & Use Default checkboxes
- Enhancement - Added support for Divi password form
- Feature - Added option to display Wordpress' default form with widgets

= 0.1.0 - 08-31-2021 =
- Initial Release
