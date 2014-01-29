=== Secondary Title ===
Contributors: thaikolja
Tags: title
Tested up to: 3.8.1
Stable tag: 0.1
Requires at least: 3.0
License: GPLv2 or later

Adds a secondary title to posts, pages and custom post types.

== Description ==

**Secondary Title** is a simple, light-weight WordPress plugin that adds an alternative title to posts and custom post types.

The plugin comes with an extra settings page which allows you to change:

*	Post types and categories the secondary title will be shown on
*	Specific post IDs for which the secondary title functionality should be activated
*	Automatically add the secondary title to the main title
*	Custom title format


== Installation ==

1. Install Secondary Title either through WordPress' native plugin installer or copy the "secondary-title" folder into your /wp-content/plugins/ directory.
2. Activate the plugin in the plug in section of your admin interface.
3. Go to Settings > Secondary Title to customize your secondary title as desired.

**IMPORTANT:** If "Insert automatically" is not set, you have to use either `<?php echo get_secondary_title(); ?>` or `<?php the_secondary_title(); ?>` in your theme to display the secondary title.

`<?php the_secondary_title($post_id, $suffix); ?>` has the following parameters:

$post_id: The ID of the post whose secondary title should be displayed. If within a loop, leave empty or use 0 to get the current post ID.

$suffix: A suffix that will be displayed at the end of the secondary title, e.g.:

`<?php the_secondary_title(0, " --- "); ?>`

will display:

*Plane crash --- Boeing 747 crashes into a river*

In case you encounter any errors or unintended behaviours, please let me know by sending a quick e-mail to kolja.nolte@gmail.com so I can update the plugin with the fixed version.

== Frequently Asked Questions ==

Still to come.

== Screenshots ==

Still to come.

== Changelog ==

= 0.1 =
* Initial Release

== Upgrade Notice ==

= 0.1 =
This is the first release of Secondary Title.