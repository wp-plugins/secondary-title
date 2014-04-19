=== Secondary Title ===
Contributors: thaikolja
Tags: title, alternative title, secondary title
Tested up to: 3.8.1
Stable tag: 0.1
Requires at least: 3.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a secondary title to posts, pages and custom post types.

== Description ==

**Secondary Title** is a simple, light-weight plugin for WordPress that adds an alternative title to posts, pages and/or custom post types.

The plugin comes with an extra settings page which allows you to customize the plugin according to your needs. You can change:

*	post types and categories the secondary title will be shown on,
*	specific post IDs for which the secondary title should be activated,
*	whether the secondary title should be automatically inserted with the standard title and
*	the format both titles are being shown (only works when "auto show" is activated).
*	the position where the secondary title input field should be displayed (above or below the standard title) within the admin interface.

**Please see www.koljanolte.com/wordpress/plugins/secondary-title/ for additional information.**

If you have any other ideas for features, please don't hesitate to submit them by [sending me an e-mail](mailto:kolja.nolte@gmail.com) and I'll try my best to implement it in the next version. Your WP.org username will be added to the plugin's contributor list, of course.

*Feel free to make Secondary Title easier to use for foreign users by [help translating the plugin on Transifex](https://www.transifex.com/projects/p/plugin-secondary-title/)*.

**Please note that this is an early version, there still may be bugs. If you encounter any problems or misbehaviors while using Secondary Title, please take a minute to report it via mail to kolja.nolte@gmail.com so that the fix can be implemented in the next version.**

== Installation ==

= How to install =

1. Install Secondary Title either through WordPress' native plugin installer (Plugins > Install) or copy the "secondary-title" folder into your /wp-content/plugins/ directory.
2. Activate the plugin in the plugin section of your admin interface.
3. Go to Settings > Secondary Title to customize the plugin as desired.

**IMPORTANT:** If "Insert automatically" is set to "No", you have to use either

`<?php echo get_secondary_title($post_id, $suffix); ?>`

or

`<?php the_secondary_title($post_id, $suffix); ?>`

in your theme file(s) to display the secondary title.

= Parameters =

*$post_id:*
(integer) (optional) The ID of the post whose secondary title should be displayed. If within a loop, leave empty or use 0 to get the current post ID.
Default: get_the_ID()

*$suffix:*
(string) (optional) A suffix that will be displayed at the end of the secondary title.
Default: None

= Examples =

1. Only display the secondary title:

`<?php the_secondary_title(0, " --- "); ?>`

will display (note the blanks):

`Plane crash --- Boeing 747 crashes into a river`

2. Using the standard and secondary title in the post's head:

`<div class="titles">
	<h2 class="secondary-title"><?php the_secondary_title(); ?></h2>
	<h1 class="title"><?php the_title(); ?></h1>
</div>`

3. Display the secondary title of the last 5 posts:

`<?php
	$query = new WP_Query(
		"post_type" => "post",
		"showposts" => 5
	);
	if($query->have_posts()) {
		echo "<ul class=\"secondary-titles\">";
		while($query->have_posts()) {
			$query->the_post();
			$secondary_title = get_secondary_title();
			echo "<li>" . get_secondary_title() . "<li />";
		}
		echo "</ul>";
	}
	else {
		echo "No posts found.";
	}
?>`

**In case you encounter any errors or unintended behaviours, please let me know by sending a quick e-mail to kolja.nolte@gmail.com so I can update the plugin with the fixed version.**

== Frequently Asked Questions ==

= How do I use this plugin? =

For installation and usage instructions, please see "Installation". There, you'll also find several examples.

= The secondary title is not being added to the standard title. =

Please verify whether the option "Insert automatically" is set to "Yes". Don't forget to enter a valid title format that uses both the standard and the secondary title. Leaving the title format blank won't show anything at all.

= The secondary title is still not shown. =

Make sure the post is in the allowed post types and/or categories set on the settings page. Also, if you have specified any post IDs, **the secondary title will only be shown on these posts**. Leave post types, categories and post IDs blank to deactivate this function.

= I have found an error and/or would like to suggest a change. =

Since Secondary Title is my first WordPress plugin, I may have missed a bug when testing it. Please be so kind to send me a quick e-mail to kolja.nolte@gmail.com so I can fix it and include it in the next version. Same goes for suggestions.

= How can I add styles (colors, fonts etc.) to the auto title? =
You can add any HTML element you want in the title format found in Settings > Secondary Title, e.g.:

`<span style='color:red;font-size:12px;'>%secondary_title%</span> %title%`

This will display the secondary title in red with a font size of 12px, the standard post title won't be changed.

== Screenshots ==

1. Secondary Title with activated "auto show" function that automatically adds the secondary title to the standard post/page title.

2. Secondary Title with "auto show" off. Displays the secondary title wherever `<?php the_secondary_title(); ?>` is called.

3. A section of the plugin's settings page on the admin interface.

== Changelog ==

= 0.4 =
* Fixed bug that showed secondary title input within the post/page overview.
* Added Italian translation (thanks to [giuseppep](https://www.transifex.com/accounts/profile/giuseppep/)).
* Added Polish transation (thanks to [pawel10](https://www.transifex.com/accounts/profile/pawel10/)).
* Updated existing translations.

= 0.3 =
* Added HTML support in title format (thanks to C0BALT).
* Added option to set the position of the secondary title input field within the admin interface (thanks to Vangelis).
* Added translation to Thai.
* Updated translation files.

= 0.2 =
* Installs default values on plugin activation.
* Added screenshots.
* Added $prefix parameter for get_secondary_title() and the_secondary_title().
* Updated FAQ.

= 0.1 =
* Initial Release.

== Upgrade Notice ==

= 0.4 =
Bug fix and translation update.

= 0.3 =
HTML support and new features.

= 0.2 =
Major changes, screenshots, FAQ, parameters.

= 0.1 =
This is the first release of Secondary Title.