=== Secondary Title ===
Contributors: thaikolja
Tags: title, alternative title, secondary title
Tested up to: 3.9
Stable tag: 0.1
Requires at least: 3.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a secondary title to posts, pages and custom post types.

== Description ==

**Secondary Title** is a simple, light-weight plugin for WordPress that adds an alternative title to posts, pages and/or custom post types.

The plugin comes with an extra settings page which allows you to customize the plugin according to your needs. You can change:

*	[post types](http://codex.wordpress.org/Post_Types) and categories the secondary title will be shown on,
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

1. Install Secondary Title either through WordPress' native plugin installer found under *Plugins > Install* or copy the *secondary-title* folder into the */wp-content/plugins/* directory of your WordPress installation.
2. Activate the plugin in the plugin section of your admin interface.
3. Go to *Settings > Secondary Title* to customize the plugin as desired.

**IMPORTANT:** If "Insert automatically" is set to "No", you have to use either

`<?php echo get_secondary_title($post_id, $prefix, $suffix); ?>`

or

`<?php the_secondary_title($post_id, $prefix, $suffix); ?>`

in your theme file(s) (e.g. single.php) to display the secondary title.

= Parameters =

`<?php get_secondary_title($post_id, $prefix, $suffix); ?>`

**$post_id** ([integer](http://codex.wordpress.org/How_to_Pass_Tag_Parameters#Integer)) (optional): The ID of the post whose secondary title should be displayed. If within a [loop](http://codex.wordpress.org/The_Loop), leave empty or use 0 to get the current post ID.
Default: get_the_ID()

**$prefix** ([string](http://codex.wordpress.org/How_to_Pass_Tag_Parameters#String)) (optional): A prefix that will be displayed before the secondary title.
Default: Empty

**$suffix** (string) (optional): A suffix that will be displayed at the end of the secondary title.
Default: Empty

`<?php the_secondary_title($post_id, $prefix, $suffix); ?>`

The same as `<?php get_secondary_title(); ?>`.

`<?php get_secondary_title_link($post_id, $options); ?>`

**$post_id** (integer) (optional): The ID of the post whose secondary title should be displayed. If within a loop, leave empty or use 0 to get the current post ID.
Default: get_the_ID()

**$options** ([array](http://codex.wordpress.org/How_to_Pass_Tag_Parameters#Arrat)) (optional): An array containing additional options:

 * *before_link*: String displayed before the link elements. **Default**: Empty
 * *after_link*: String displayed after the link elements. **Default**: Empty
 * *before_text*: String displayed before the link text. **Default**: Empty
 * *before_text*: String displayed after the link text. **Default**: Empty
 * *link_target*: target="" attribute for the "a href" element. **Default**: _self
 * *link_title*: title="" attribute for the "a href" element (displayed on mouseover). **Default**: Empty
 * *link_id*: id="" attribute for the "a href" element. **Default**: secondary-title-link-$post_id
 * *link_class*: class="" attribute for the "a href" element. **Default**: secondary-title-link

= Examples =

1. Only display the secondary title:

`<?php the_secondary_title(); ?>`

2. Using the standard and secondary title in the post's head:

`<div class="titles">
	<h2 class="secondary-title">+++ <?php the_secondary_title(); ?> +++</h2>
	<h1 class="title"><?php the_title(); ?></h1>
</div>`

**Displays:**
+++ Plane missing +++
Malaysian Airlines flight MH370 lost over Gulf of Thailand

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

4. Display the secondary title of a specific post:

`<?php
	$secondary_title = get_secondary_title(28);
?>
<h1><?php echo $secondary_title; ?></h1>`

This will display the post with the post ID 28.

5. Display the secondary title in red and the standard title in the default color:

`<span style="color:red;"><?php the_secondary_title(); ?></span> <?php the_title(); ?>`

**In case you encounter any errors or unintended behaviours, please let me know by sending a quick e-mail to kolja.nolte@gmail.com so I can update the plugin with the fixed version.**

== Frequently Asked Questions ==

= How do I use this plugin? =

For installation and usage instructions, please see [Installation](http://wordpress.org/plugins/secondary-title/installation/) either check the [official documentation](http://www.koljanolte.com/wordpress/plugins/secondary-title/). There, you'll also find several examples.

= The secondary title is not being added to the standard title. =

Please verify whether the option "Insert automatically" is set to "Yes". Don't forget to enter a valid title format, use the placeholders %title% for the standard title and %secondary_title% for the secondary title. Leaving the title format blank won't show anything at all.

= The secondary title still doesn't show up. =

Make sure the post is among the allowed post types and/or categories set under *Settings > Secondary Title*. Also, if you have specified any post IDs, **the secondary title will only be shown on these posts**. Leave post types, categories and post IDs blank to deactivate this function.

= How can I add styles (colors, fonts etc.) to the auto title? =
You can add any HTML element you want in the title format found in *Settings > Secondary Title*, e.g.:

`<span style='color:red;font-size:12px;'>%secondary_title%</span> %title%`

This will display the secondary title in red with a font size of 12px, the standard post title won't be changed.

If you want the styled secondary title only to be displayed in a certain place (e.g. inside of the home posts), you will have to define a class. For that, open your *style.css* of

= How can I add styles with the manual secondary title? =
To style the output of `<?php the_secondary_title(); ?>` or `<?php get_secondary_title(); ?>`, you can use HTML in PHP:
`<?php
	echo '<span style="color:red;font-size:12px;">' . get_secondary_title() . '</span>';
?>`

Same as above, this will display the secondary title in red and with a font size of 12px.

= I want the secondary title only to be displayed in posts, not in the sidebar etc. =

Since version 0.6 you can set whether the the secondary title should be should be shown everywhere or exclusively on the main post. If activated, it won't be shown in sidebars, menu items etc.

= I have found an error and/or would like to suggest a change. =

Since Secondary Title is my first WordPress plugin, I may have missed a bug when testing it. Please be so kind to send me a quick e-mail to kolja.nolte@gmail.com so I can fix it and include it in the next version. Same goes for suggestions.

== Screenshots ==

1. Secondary Title with activated "auto show" function that automatically adds the secondary title to the standard post/page title.

2. Secondary Title with "auto show" off. Displays the secondary title wherever `<?php the_secondary_title(); ?>` is called.

3. A section of the plugin's settings page on the admin interface.

== Changelog ==

= 0.6 =
* Added "Only show in main post" setting.
* Fixed minor jQuery bug on admin interface.
* Updated FAQ.

= 0.5.1 =
* Fixed bug that falsely added slashes to HTML attributes in title format.
* Fixed jQuery bug in the admin posts/
* Added `<?php has_secondary_title(); ?>` function. See [the official documentation](http://www.koljanolte.com/koljanolte.com/wordpress/plugins/secondary-title/#Parameters) for more information.

= 0.5 =
* Fixed bug where the secondary title was not shown if the standard title contains "..." (thanks to Vangelis).
* Added "select/unselect all" function for checkbox lists on settings page.
* Added secondary title display in admin posts/pages list.
* Added `<?php get_secondary_title_link($post_id, $options); ?>` and `<?php the_secondary_title_link($post_id, $options); ?>` functions
  to quickly create the secondary title as a link to its post. See [the official documentation](http://www.koljanolte.com/koljanolte.com/wordpress/plugins/secondary-title/#Parameters) for more information.
* Updated documentation/readme.txt.

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
* Added $suffix and $prefix parameter for get_secondary_title() and the_secondary_title().
* Updated FAQ.

= 0.1 =
* Initial Release.

== Upgrade Notice ==

= 0.6 =
Bug fixes, setting added.

= 0.5.1 =
Hotfix for 0.5.

= 0.5 =
Bug fixes and some more features.

= 0.4 =
Bug fix and translation update.

= 0.3 =
HTML support and new features.

= 0.2 =
Major changes, screenshots, FAQ, parameters.

= 0.1 =
This is the first release of Secondary Title.