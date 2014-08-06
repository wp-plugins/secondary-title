=== Secondary Title ===
Contributors: thaikolja
Tags: title, alternative title, secondary title
Tested up to: 3.9.2
Stable tag: 0.1
Requires at least: 3.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a secondary title to posts, pages and custom post types.

== Description ==

**Secondary Title** is a simple, light-weight plugin for WordPress that adds an alternative title to posts, pages and/or custom post types.

The plugin comes with an extra settings page which allows you to customize the plugin according to your needs. You can change:

*	[post types](http://codex.wordpress.org/Post_Types), categories and specific post IDs the secondary title will be
shown on,
*	whether the secondary title should be automatically inserted with the standard title (*Auto show*),
*	the format both titles are being shown (only works when *Auto show* is activated),
*	the position where the secondary title input field should be displayed (above or below the standard title) within the admin interface,
* whether the secondary title should only be displayed in the main post and not within widgets etc.,
* if the secondary title should be added to the [permalinks](http://codex.wordpress.org/Using_Permalinks).

**Please the [official website](http://www.koljanolte.com/wordpress/plugins/secondary-title/) for a full documentation.**

If you have any other ideas for features, please don't hesitate to submit them by [sending me an e-mail](mailto:kolja.nolte@gmail.com) and I'll try my best to implement it in the next version. Your WP.org username will be added to the plugin's contributor list, of course (if you provide one).

*Feel free to make Secondary Title easier to use for foreign users by [help translating the plugin on Transifex](https://www.transifex.com/projects/p/secondary-title/)*.

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

**For a more detailed documentation with parameters, functions and examples, please see the [official documentation](http://www.koljanolte.com/wordpress/plugins/secondary-title/)**.

== Frequently Asked Questions ==

= How do I use this plugin? =

For installation instructions, please see [Installation](http://wordpress.org/plugins/secondary-title/installation/).
 A more detailed documentation with parameters, functions and usage examples can be found on the [official website](http://www.koljanolte.com/wordpress/plugins/secondary-title/).

= The secondary title is not being added to the standard title. =

Please verify whether the option *Insert automatically* is set to *Yes*, otherwise it will only be displayed when you call `<?php echo get_secondary_title(); ?>` or `<?php the_secondary_title(); ?>`. If you're using the auto show function. don't forget to enter a valid title format; use the placeholders `%title%` for the standard title and `%secondary_title%` for the secondary title. Leaving the title format blank won't show anything at all.

= The secondary title still doesn't show up. =

Make sure the post is among the allowed post types and/or categories set under *Settings > Secondary Title*. Also, if you have specified any post IDs, **the secondary title will only be shown with these posts**. Leave post types, categories and post IDs blank to deactivate this function.

= How can I add styles (colors, fonts etc.) to the auto title? =
You can add any HTML element you want in the title format found in *Settings > Secondary Title*, e.g.:

`<span style='color:red;font-size:12px;'>%secondary_title%</span> %title%`

This will display the secondary title in red with a font size of 12px; the standard post title won't be changed.

If you want the secondary title only to be displayed in a certain place (e.g. inside of the home posts),
you will have to wrap it in a class within the title format (e.g. `<span
class="secondary-title">%secondary_title%</span> %title%`)and use `display:none;` in your *style.css* to hide
it and `display:none;` to show it only in the selected area(s).

For example, to hide the secondary title on the front page (home), the CSS passage my look like this:

`/** This will hide the secondary title everywhere */
.secondary-title {
	display:none;
}
/** This makes it visible only on the front page */
.home .secondary-title {
	display:block;
}`

= How can I add styles with the manual secondary title? =
To style the output of `<?php the_secondary_title(); ?>` or `<?php get_secondary_title(); ?>`, you can use HTML in PHP:
`<?php
	echo '<span style="color:red;font-size:12px;">' . get_secondary_title() . '</span>';
?>`

Same as above, this will display the secondary title in red and with a font size of 12px.

= I'd like the secondary title only to be displayed in posts, not in the sidebar etc. =

Since version 0.6 you can set whether the the secondary title should be should be shown everywhere or exclusively in the main post. If activated, it won't be shown in sidebars, menu items etc.

= How to display the secondary title above/below the main title with turned on auto show? =
You can insert line breaks with the `<br />` HTML tag. Example:
`%secondary_title%<br />%title%`

= I'm using "WordPress SEO Plugin by Yoast". How can I add the secondary title to the templates? =

Yoast's SEO plugin comes with the handy feature that allows you to use any post meta data as a tag within your templates. Since the secondary title is saved within the post meta called _secondary_title, the template tag that has to be used in order to display the secondary title is `%%cf__secondary_title%%` (note the double underscore after cf).

= I have found an error and/or would like to suggest a change. =

Since Secondary Title is my first WordPress plugin, I may have missed a bug when testing it. Please be so kind to [send me an e-mail](mailto:kolja.nolte@gmail.com) so I can fix it and include it in the next version. Same goes for suggestions.

== Screenshots ==

1. Secondary Title with activated "auto show" function that automatically adds the secondary title to the standard post/page title.

2. Secondary Title with "auto show" off. Displays the secondary title wherever `<?php the_secondary_title(); ?>` is called.

3. A section of the plugin's settings page on the admin interface.

== Changelog ==

= 0.9 =
* Removed "report bug" e-mail form due to compatibility issues.
* Fixed bug that deleted the secondary title on selected posts when using "bulk edit" (thanks to [JacobSchween](http://wordpress.org/support/topic/bulk-edit-deletes-secondary-titles)).
* Fixed bug that occurred when saving a custom menu (only visible with WP_DEBUG).
* Updated translations.
* Several small changes that aren't important enough to be mentioned here.

= 0.8 =
* Some new minor functions and changes on the settings page.
* Allowed to use `%title%` and `%secondary_title%` variable on settings page in *Title format* more than once.
* Added option to [use secondary title in permalinks](http://wordpress.org/support/topic/feature-request-add-secondary-title-to-permalinks?replies=3).
* Added filter hooks to `<?php get_secondary_title(); ?>`, `<?php the_secondary_title(); ?>` and
`<?php get_secondary_title_link(); ?>`.
* Added French translation (thanks to [fxbenard](https://www.transifex.com/accounts/profile/fxbenard/)).
* Updated existing translations.
* Fixed bug that prevented the secondary title to be updated when empty.
* Renamed `<?php get_filtered_post_types(); ?>` to `<?php get_secondary_title_filtered_post_types(); ?>` to avoid
possible
conflicts.

= 0.7 =
* Restructured and split up plugin code into different files for better handling.
* Added *Secondary title* column to posts/pages overview.
* Added secondary title input field to quick edit box on posts/pages overview.
* Added bug report form to settings page.
* Removed secondary title from above/below the standard title on posts/page overview.
* Renamed functions to minimize conflicts with other plugins.
* Updated screenshots.
* Updated translations.
* Bug fixes.

= 0.6 =
* Added compatibility with Word Filter Plus plugin.
* Added *Only show in main post* setting.
* Fixed minor jQuery bug on admin interface.
* Updated FAQ.

= 0.5.1 =
* Fixed bug that falsely added slashes to HTML attributes in title format.
* Fixed jQuery bug in the admin posts/
* Added `<?php has_secondary_title(); ?>` function. See [the official documentation](http://www.koljanolte.com/koljanolte.com/wordpress/plugins/secondary-title/#Parameters) for more information.

= 0.5 =
* Fixed bug where the secondary title was not shown if the standard title contains "..." (thanks to Vangelis).
* Added *Select all* and *Unselect all* script for checkbox lists on settings page.
* Added secondary title display in admin posts/pages list.
* Added `<?php get_secondary_title_link($post_id, $options); ?>` and `<?php the_secondary_title_link($post_id, $options); ?>` functions
  to quickly create the secondary title as a link to its post. See [the official documentation](http://www.koljanolte.com/koljanolte.com/wordpress/plugins/secondary-title/#Parameters) for more information.
* Updated documentation/readme.txt.

= 0.4 =
* Fixed bug that showed secondary title input within the post/page overview.
* Added Italian translation (thanks to [giuseppep](https://www.transifex.com/accounts/profile/giuseppep/)).
* Added Polish translation (thanks to [pawel10](https://www.transifex.com/accounts/profile/pawel10/)).
* Updated existing translations.

= 0.3 =
* Added HTML support in title format (thanks to C0BALT).
* Added option to set the position of the secondary title input field within the admin interface (thanks to Vangelis).
* Added translation to Thai.
* Updated translation files.

= 0.2 =
* Installs default values on plugin activation.
* Added screenshots.
* Added $prefix and $suffix parameter for `<?php get_secondary_title(); ?>` and `<?php the_secondary_title(); ?>`.
* Updated FAQ.

= 0.1 =
* Initial Release.

== Upgrade Notice ==

= 0.9 =
Bug fixes.

= 0.8 =
Permalinks support, bug fixes, translation updates.

= 0.7 =
Major changes; restructured plugin files, added "Secondary title" column to posts/page overview and more.

= 0.6 =
Bug fixes, setting added, compatibility with Word Filter Plus plugin.

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